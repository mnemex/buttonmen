<?php

require_once "engine/BMAttack.php";

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-21 at 15:12:02.
 */
class BMAttackPowerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BMAttackPower
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = BMAttackPower::get_instance();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }



    /**
     * @covers BMAttackPower::validate_attack
     * @todo   Implement testValidate_attack().
     */
    public function testValidate_attack()
    {
        $game = new BMGame;

        $die1 = new BMDie;
        $die1->init(6);
        $die1->value = 6;

        $die2 = new BMDie;
        $die2->init(6);
        $die2->value = 1;

        // Basic error handling
        $this->assertFalse($this->object->validate_attack($game, array(), array()));
        $this->assertFalse($this->object->validate_attack($game, array($die1), array()));
        $this->assertFalse($this->object->validate_attack($game, array(), array($die1)));

        // Basic attacks

        // 6 > 1
        $this->assertTrue($this->object->validate_attack($game, array($die1), array($die2)));


        // 1 ! > 6
        $this->assertFalse($this->object->validate_attack($game, array($die2), array($die1)));

        // 6 == 6
        $this->assertTrue($this->object->validate_attack($game, array($die1), array($die1)));

        // 1 == 1
        $this->assertTrue($this->object->validate_attack($game, array($die2), array($die2)));


        // Attacks with helpers
        $die3 = new BMDie;
        $die3->init(6, array("AVTesting"));
        $die3->value = 6;

        $die4 = new BMDie;
        $die4->init(6);
        $die4->value = 2;

        $game->activeDieArrayArray = array(array($die3), array());
        $game->attack = array(0, 1, array(), array(), '');
        $help = $this->object->collect_helpers($game, array(), array());


        // 1 + 1 ! >= 6
        $this->assertFalse($this->object->validate_attack($game, array($die2), array($die1)));

        // 1 + 1 == 2
        $this->assertTrue($this->object->validate_attack($game, array($die2), array($die4)));

        // 6 > 1
        $this->assertTrue($this->object->validate_attack($game, array($die1), array($die2)));

        // 1 == 1
        $this->assertTrue($this->object->validate_attack($game, array($die2), array($die2)));

        // With a few more of those, 1 can take a 6.
        $die5 = clone $die3;
        $die6 = clone $die3;
        $die7 = clone $die3;
        $die8 = clone $die3;

        $game->activeDieArrayArray = array(array($die3, $die5, $die6, $die7, $die8), array());
        $help = $this->object->collect_helpers($game, array(), array());

        $this->assertTrue($this->object->validate_attack($game, array($die2), array($die1)));
    }


    /**
     * @covers BMAttackPower::find_attack
     * @depends testValidate_attack
     * @todo   Implement testFind_attack().
     */
    public function testFind_attack()
    {
        $game = new BMGame;

        // we find nothing when there are no attackers
        $this->assertFalse($this->object->find_attack($game));

        // Load some dice into the attack.
        $die1 = new BMDie;
        $die1->init(6);
        $die1->value = 6;

        $this->object->add_die($die1);

        $die2 = new BMDie;
        $die2->init(6);
        $die2->value = 1;

        $this->object->add_die($die2);

        // we find nothing when there are no defenders
        $this->assertFalse($this->object->find_attack($game));


        $die3 = new BMDie;
        $die3->init(6);
        $die3->value = 6;

        $die4 = new BMDie;
        $die4->init(20);
        $die4->value = 7;


        $game->activeDieArrayArray = array(array(), array($die3));
        $game->attack = array(0, 1, array(), array(), '');

        $this->assertTrue($this->object->find_attack($game));

        $game->activeDieArrayArray = array(array(), array($die4));

        $this->assertFalse($this->object->find_attack($game));

        // with both
        $game->activeDieArrayArray = array(array(), array($die4, $die3));

        $this->assertTrue($this->object->find_attack($game));

        // with an assist

        // Attacks with helpers
        $die5 = new BMDie;
        $die5->init(6, array("AVTesting"));
        $die5->value = 1;

        $game->activeDieArrayArray = array(array($die5), array($die4));

        $this->assertTrue($this->object->find_attack($game));
    }

    /**
     * @covers BMAttackPower::calculate_contributions
     * @todo   Implement testCalculate_contributions
     */
    public function testCalculate_contributions() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}

