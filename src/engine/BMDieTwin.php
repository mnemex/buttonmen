<?php

class BMDieTwin extends BMDie {
    public $dice;

    public function init($sidesArray, array $skills = NULL) {
        if (!is_array($sidesArray)) {
            throw new InvalidArgumentException('sidesArray must be an array.');
        }

        if (2 != count($sidesArray)) {
            throw new InvalidArgumentException('sidesArray must have exactly two elements.');
        }

        $this->add_multiple_skills($skills);

        foreach ($sidesArray as $dieIdx => $sides) {
            $this->dice[$dieIdx] =
                BMDie::create_from_string_components($sides, $skills);
        }

        if ($this->dice[0] instanceof BMDieSwing &&
            $this->dice[1] instanceof BMDieSwing &&
            $this->dice[0]->swingType != $this->dice[1]->swingType) {
            throw new InvalidArgumentException('A twin die can only have one swing type.');
        }

        if ($this->dice[0] instanceof BMDieSwing) {
            $this->swingType = $this->dice[0]->swingType;
        } elseif ($this->dice[1] instanceof BMDieSwing) {
            $this->swingType = $this->dice[1]->swingType;
        }

        $this->recalc_max_min();
    }

    public static function create($sidesArray, array $skills = NULL) {
        if (!is_array($sidesArray)) {
            throw new InvalidArgumentException('sidesArray must be an array.');
        }

        $die = new BMDieTwin;
        $die->init($sidesArray, $skills);

        return $die;
    }

    public function activate() {
        $newDie = clone $this;

        $this->run_hooks(__FUNCTION__, array('die' => $newDie));

        foreach ($this->dice as $die) {
            if ($die instanceof BMDieSwing) {
                $this->ownerObject->request_swing_values(
                    $newDie,
                    $die->swingType,
                    $newDie->playerIdx
                );
            }
            $newDie->valueRequested = TRUE;
        }

        $this->ownerObject->add_die($newDie);
    }

    public function roll($successfulAttack = FALSE) {
        if (is_null($this->max)) {
            return;
        }

        $this->value = 0;
        foreach ($this->dice as &$die) {
            $die->roll();
            $this->value += $die->value;
        }

        $this->run_hooks(__FUNCTION__, array('isSuccessfulAttack' => $successfulAttack));
    }

    // Print long description
    public function describe($isValueRequired = FALSE) {
        if (!is_bool($isValueRequired)) {
            throw new InvalidArgumentException('isValueRequired must be boolean');
        }

        $skillStr = '';
        if (count($this->skillList) > 0) {
            foreach (array_keys($this->skillList) as $skill) {
                $skillStr .= "$skill ";
            }
        }

        $typeStr = '';
        if ($this->dice[0] instanceof BMDieSwing &&
            $this->dice[1] instanceof BMDieSwing) {
            $typeStr = "Twin {$this->dice[0]->swingType} Swing Die";
        } else {
            $typeStr = 'Twin Die';
        }

        $sideStr = '';
        if (isset($this->dice[0]->max)) {
            if ($this->dice[0]->max == $this->dice[1]->max) {
                $sideStr = " (both with {$this->dice[0]->max} sides)";
            } else {
                $sideStr = " (with {$this->dice[0]->max} and {$this->dice[1]->max} sides)";
            }
        }

        $valueStr = '';
        if ($isValueRequired && isset($this->value)) {
            $valueStr = " showing {$this->value}";
        }

        $result = "{$skillStr}{$typeStr}{$sideStr}{$valueStr}";

        return $result;
    }

    public function split() {
        $newdie = clone $this;

        foreach ($this->dice as $dieIdx => &$die) {
            $splitDieArray = $die->split();
            $this->dice[$dieIdx] = $splitDieArray[0];
            $newdie->dice[$dieIdx] = $splitDieArray[1];
        }

        $this->recalc_max_min();
        $newdie->recalc_max_min();

        $splitDice = array($this, $newdie);

        $this->run_hooks(__FUNCTION__, array('dice' => &$splitDice));

        return $splitDice;
    }

    public function set_swingValue($swingList) {
        $valid = TRUE;

        foreach ($this->dice as &$die) {
            if ($die instanceof BMDieSwing) {
                $valid &= $die->set_swingValue($swingList);
                $this->swingValue = $die->swingValue;
            }
        }

        $this->recalc_max_min();

        return $valid || !isset($this->swingType);
    }

    // Return all information about a die which is useful when
    // constructing an action log entry, in the form of an array.
    // This function exists so that BMGame can easily compare the
    // die state before the attack to the die state after the attack.
//    public function get_action_log_data() {
//       $recipe = $this->get_recipe();
//       return(array(
//           'recipe' => $recipe,
//           'min' => $this->min,
//           'max' => $this->max,
//           'value' => $this->value,
//           'doesReroll' => $this->doesReroll,
//           'captured' => $this->captured,
//           'recipeStatus' => $recipe . ':' . $this->value,
//       ));
//    }

    protected function recalc_max_min() {
        $this->min = 0;
        $this->max = 0;

        foreach ($this->dice as $die) {
            if (is_null($die->min) ||
                is_null($die->max)) {
                $this->min = NULL;
                $this->max = NULL;
                break;
            }
            $this->min += $die->min;
            $this->max += $die->max;
        }
    }
}
