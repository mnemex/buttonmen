<?php

class BMAttackShadow extends BMAttackPower {
    public $type = 'Shadow';

    public function validate_attack($game, array $attackers, array $defenders) {
        if (1 != count($attackers) || 1 != count($defenders)) {
            return FALSE;
        }

        $attacker = $attackers[0];
        $defender = $defenders[0];

        $doesAttackerHaveShadow = array_key_exists('Shadow', $attacker->skillList);
        $doesAttackerHaveQueer = array_key_exists('Queer', $attacker->skillList);
        $isAttackerOdd = (1 == $attacker->value % 2);

        $isDieLargeEnough = $attacker->max >=
                            $defender->defense_value($this->type);

        $attackValueArray = $attacker->attack_values($this->type);
        assert(1 == count($attackValueArray));
        $attackValue = $attackValueArray[0];
        $defenseValue = $defender->defense_value($this->type);
        $isValueSmallEnough = $attackValue <= $defenseValue;

        $canAttackerPerformThisAttack =
            $attacker->is_valid_attacker($this->type, $attackers, $defenders);
        $isDefenderValidTargetForThisAttack =
            $defender->is_valid_target($this->type, $attackers, $defenders);

        return (($doesAttackerHaveShadow ||
                 ($doesAttackerHaveQueer && $isAttackerOdd)) &&
                $isDieLargeEnough &&
                $isValueSmallEnough &&
                $canAttackerPerformThisAttack &&
                $isDefenderValidTargetForThisAttack);
    }
}

?>
