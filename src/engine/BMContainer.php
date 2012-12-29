<?php

require_once 'BMDie.php';

/*
 * BMContainer: Managing die groups.
 *
 * @author: Julian Lighton
 */

class BMContainer {
    public $contents = array();

    // keyed by the Names of the skills that the class has, with values of
    // the skill class's name
    //
    // Not that the value is needed, but we use the keys to track
    // which skills we have to avoid duplication.
    protected $skillList = array();


    // Make the container's special choices, then activate the
    // appropriate contents, adding the container's skills to them
    // first
    //
    // Dice return activated versions of themselves. Containers feed
    // the activated dice to the game and return NULL
    //
    // I don't like the differing semantics, but the containers can't
    // accumulate all the dice and then return them to the game
    // because that would leave the list of to-be-activated dice
    // inaccessible by other dice. If they're in the game, they can be
    // looked at and manipulated as necessary.
    //
    // possible rework: activate takes &$dlist = NULL as third
    // param. If it's null, the container makes one, so the outermost
    // container's list is being used for all subcontainers. Cleans up
    // most of the semantics, but doesn't actually gain functionality.
    // (Seems to complicate the process of getting dice out to the game.)
    public function activate($game, $owner) {
        foreach ($this->contents as $thing) {
            foreach ($this->skillList as $skill => $class) {
                $thing->add_skill($skill);
            }
            $a = $thing->activate($game, $owner);
            
            // only dice return anything from activation
            if ($a) {
                $game->add_die($owner, $a);
            }
        }
        return NULL;
    }

    // add a die or container to the end of the container
    public function add_thing($thing) {
        // Only dice and containers
        if (is_a($thing, "BMContainer")  || is_a($thing, "BMDie")) {
            $this->contents[] = $thing;
            return $thing;
        }
        return NULL;
    }

    // skill management
    public function add_skill($skill) {
        $skillClass = "BMSkill$skill";

        // Don't add skills that are already added
        if (!array_key_exists($skill, $this->skillList)) {
            $this->skillList[$skill] = $skillClass;
        }

    }

    public function has_skill($skill) {
        return array_key_exists($skill, $this->skillList);
    }

    public function remove_skill($skill) {
        if (!$this->has_skill($skill)) {
            return FALSE;
        }

        unset($this->skillList[$skill]);

        return TRUE;

    }

    public function count_dice() {
        return count($this->list_dice());
    }

    public function list_dice() {
        $list = array();

        foreach ($this->contents as $thing) {
            if (is_a($thing, "BMDie")) {
                $list[] = $thing;
            }
            else {
                $list = array_merge($list, $thing->list_dice());
            }
        }
        return $list;
    }


    // Counts how many dice within the container have the requested skill
    //
    // This is the theoretical count. Certain subclasses of
    // BMContainer could confuse the issue.
    public function count_skill($skill) {
        $total = 0;

        if ($this->has_skill($skill)) {
            $total = $this->count_dice();
        }
        else {
            foreach ($this->contents as $thing) {
                if (is_a($thing, "BMContainer")) {
                    $total += $thing->count_skill($skill);
                } else {
                    if ($thing->has_skill($skill)) {
                        $total++;
                    }
                }
            }
        }
        return $total;
    }



    // create the container from an array of dice and containers if
    // any elements of the array are themselves arrays, we will make
    // them as containers. Skills will only be added to the outermost
    // container
    public static function create_from_list($contents, $skills = array()) {
        $cont = new BMContainer;

        foreach ($contents as $thing) {
            if (is_array($thing)) {
                $cont->add_thing(BMContainer::create_from_list($thing));
            }
            elseif (!$cont->add_thing($thing)) {
                throw new UnexpectedValueException("Invalid container contents");
            }
        }

        foreach ($skills as $s) {
            $cont->add_skill($s);
        }

        return $cont;
    }

    // utility methods

    // If we clone the container, we must clone all contents as well
    public function __clone() {
        foreach ($this->contents as $i => $thing) {
            $this->contents[$i] = clone $thing;
        }
    }
}


class BMSelectContainer extends BMContainer {

}

class BMPlasmaContainer extends BMContainer {

}

class BMReserveContainer extends BMContainer {

}

// These last two may not be needed; the engine could possibly use
// generic containers and simply open them first or last.

// Special container for Auxiliary dice and the like. Activated only
// at the start of games.
class BMInitialContainer extends BMContainer {

}

// Special container that is opened last each round, so the dice
// within can replace other dice.
class BMSideboardContainer extends BMContainer {

}

?>