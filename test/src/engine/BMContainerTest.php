<?php

require_once "TestDummyBMSkillTesting.php";
require_once "TestDummyBMSkillTesting2.php";
require_once "TestDummyBMDieTesting.php";
require_once "TestDummyBMContTesting.php";

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-21 at 15:05:15.
 */
class BMContainerTest extends PHPUnit_Framework_TestCase {
    /**
     * @var BMContainer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new BMContainer;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers BMContainer::add_thing
     */
    public function testAdd_thing()
    {
        $die1 = new BMDie;
        $die2 = new BMDie;
        $cont = new BMContainer;
        $die3 = new TestDummyBMDieTesting;
        $cont2 = new TestDummyBMContTesting;

        // Adding non-die or containers should fail
        $this->assertEmpty($this->object->contents);
        $this->assertNull($this->object->add_thing("thing!"));
        $this->assertEmpty($this->object->contents);

        $this->assertEmpty($this->object->contents);
        $this->assertNull($this->object->add_thing($this));
        $this->assertEmpty($this->object->contents);

        // add dice
        $this->assertNotNull($this->object->add_thing($die1));
        $this->assertNotEmpty($this->object->contents);
        $this->assertEquals(count($this->object->contents), 1);
        $this->assertContains($die1, $this->object->contents);

        // test return value; should return the added object
        $this->assertTrue($die2 === $this->object->add_thing($die2));
        $this->assertNotEmpty($this->object->contents);
        $this->assertEquals(count($this->object->contents), 2);
        $this->assertContains($die1, $this->object->contents);
        $this->assertContains($die2, $this->object->contents);

        // test adding duplicates; should work
        $this->assertTrue($die1 === $this->object->add_thing($die1));
        $this->assertNotEmpty($this->object->contents);
        $this->assertEquals(count($this->object->contents), 3);
        $this->assertContains($die1, $this->object->contents);
        $this->assertContains($die2, $this->object->contents);

        // ordering correct?
        $this->assertTrue($die1 === $this->object->contents[0]);
        $this->assertTrue($die2 === $this->object->contents[1]);
        $this->assertTrue($die1 === $this->object->contents[2]);

        // add a container
        $this->assertTrue($cont === $this->object->add_thing($cont));
        $this->assertNotEmpty($this->object->contents);
        $this->assertEquals(count($this->object->contents), 4);
        $this->assertContains($cont, $this->object->contents);

        // add subclasses
        $this->assertTrue($die3 === $this->object->add_thing($die3));
        $this->assertTrue($cont2 === $this->object->add_thing($cont2));

    }

    /**
     * @covers BMContainer::add_skill
     * @todo   Implement testAdd_skill().
     */
    public function testAdd_skill()
    {
        // Check that the skill list is indeed empty
        $sl = PHPUnit_Framework_Assert::readAttribute($this->object, "skillList");

        $this->assertEmpty($sl, "Skill list not initially empty.");

        $this->object->add_skill("Testing", "TestDummyBMSkillTesting");

        $sl = PHPUnit_Framework_Assert::readAttribute($this->object, "skillList");
        $this->assertNotEmpty($sl, "Skill list should not be empty.");
        $this->assertEquals(count($sl), 1, "Skill list contains more than it should.");
        $this->assertArrayHasKey('Testing', $sl, "Skill list doesn't contain 'Testing'");
        $this->assertEquals($sl["Testing"], "TestDummyBMSkillTesting", "Incorrect stored classname for 'Testing'");

        // Another skill

        $this->object->add_skill("Testing2", "TestDummyBMSkillTesting2");

        $sl = PHPUnit_Framework_Assert::readAttribute($this->object, "skillList");
        $this->assertNotEmpty($sl, "Skill list should not be empty.");
        $this->assertEquals(count($sl), 2, "Skill list contains more than it should.");
        $this->assertArrayHasKey('Testing', $sl, "Skill list doesn't contain 'Testing'");
        $this->assertArrayHasKey('Testing2', $sl, "Skill list doesn't contain 'Testing2'");
        $this->assertEquals($sl["Testing2"], "TestDummyBMSkillTesting2", "Incorrect stored classname for 'Testing2'");


        // Redundancy

        $this->object->add_skill("Testing");

        $sl = PHPUnit_Framework_Assert::readAttribute($this->object, "skillList");
        $this->assertEquals(count($sl), 2, "Skill list contains more than it should.");
        $this->assertArrayHasKey('Testing', $sl, "Skill list doesn't contain 'Testing'");
        $this->assertArrayHasKey('Testing2', $sl, "Skill list doesn't contain 'Testing2'");

    }

    /**
     * @covers BMContainer::has_skill
     * @depends testAdd_skill
     */
    public function testHas_skill() {
        $this->object->add_skill("Testing");
        $this->object->add_skill("Testing2");
        $this->assertTrue($this->object->has_skill("Testing"));
        $this->assertTrue($this->object->has_skill("Testing2"));
        $this->assertFalse($this->object->has_skill("Testing3"));
    }


    /**
     * @covers BMContainer::list_dice
     * @depends testAdd_thing
     */
    public function testList_dice() {
        $die1 = new BMDie;
        $die2 = new BMDie;
        $die3 = new BMDie;
        $die4 = new BMDie;

        $dice = array(new BMDie, new BMDie, new BMDie);

        $cont1 = new BMContainer;
        $cont2 = new BMContainer;
        $cont3 = new BMContainer;
        $cont4 = new BMContainer;

        $this->assertEmpty($cont1->list_dice());

        foreach ($dice as $d) {
            $cont1->add_thing($d);
        }

        $this->assertEquals(3, count($cont1->list_dice()));

        foreach ($dice as $d) {
            $tmp = $cont1->list_dice();

            $count = 0;
            foreach ($tmp as $listedDie) {
                if ($d === $listedDie) { $count++; }
            }
            $this->assertEquals(1, $count);

# This version of the above test fails, array_search returning 0 twice
#  running. No clue why.
#
#            $target = array_search($d, $tmp);
#            $this->assertFalse($target === FALSE);
#
#            $this->assertTrue($d === $tmp[$target]);
        }

        // Nested containers
        $cont2->add_thing($die1);
        $cont2->add_thing($cont1);

        $dice[] = $die1;

        $this->assertEquals(4, count($cont2->list_dice()));

        foreach ($dice as $d) {
            $tmp = $cont2->list_dice();

            $count = 0;
            foreach ($tmp as $listedDie) {
                if ($d === $listedDie) { $count++; }
            }
            $this->assertEquals(1, $count);
        }

        // Multi-level nesting
        $cont3->add_thing($die2);
        $cont3->add_thing($cont2);
        $cont3->add_thing($die3);

        $dice[] = $die2;
        $dice[] = $die3;

        $this->assertEquals(6, count($cont3->list_dice()));

        foreach ($dice as $d) {
            $tmp = $cont3->list_dice();

            $count = 0;
            foreach ($tmp as $listedDie) {
                if ($d === $listedDie) { $count++; }
            }
            $this->assertEquals(1, $count);
        }

        // Multiple containers on same level
        $cont4->add_thing($die4);
        $cont3->add_thing($cont4);
        $dice[] = $die4;


        $this->assertEquals(7, count($cont3->list_dice()));

        foreach ($dice as $d) {
            $tmp = $cont3->list_dice();

            $count = 0;
            foreach ($tmp as $listedDie) {
                if ($d === $listedDie) { $count++; }
            }
            $this->assertEquals(1, $count);
        }
    }

    /**
     * @covers BMContainer::count_dice
     * @depends testList_dice
     * @depends testAdd_thing
     */
    public function testCount_dice() {
        $die1 = new BMDie;
        $die2 = new BMDie;
        $die3 = new BMDie;
        $die4 = new BMDie;

        $dice = array(new BMDie, new BMDie, new BMDie);

        $cont1 = new BMContainer;
        $cont2 = new BMContainer;
        $cont3 = new BMContainer;
        $cont4 = new BMContainer;

        $this->assertEquals(0, $this->object->count_dice());

        foreach ($dice as $d) {
            $cont1->add_thing($d);
        }

        $this->assertEquals(3, $cont1->count_dice());

        // Nested containers
        $cont2->add_thing($die1);
        $cont2->add_thing($cont1);


        $this->assertEquals(4, $cont2->count_dice());


        // Multi-level nesting
        $cont3->add_thing($die2);
        $cont3->add_thing($cont2);
        $cont3->add_thing($die3);

        $this->assertEquals(6, $cont3->count_dice());

        // Multiple containers on same level
        $cont4->add_thing($die4);
        $cont3->add_thing($cont4);


        $this->assertEquals(7, $cont3->count_dice());
    }

    /**
     * @covers BMContainer::count_skill
     * @depends testAdd_skill
     * @depends testHas_skill
     * @depends testAdd_thing
     */
    public function testCount_skill()
    {
        $die1 = new BMDie;
        $die2 = new BMDie;
        $die3 = new BMDie;
        $cont = new BMContainer;

        // Trivial case
        $this->assertEquals(0, $this->object->count_skill("Testing"));
        $this->assertEquals(0, $this->object->count_skill("Testing2"));

        // Non-existent skill
        $this->assertEquals(0, $this->object->count_skill("Untested"));

        $this->object->add_thing($die1);
        $this->object->add_thing($die2);

        $die1->add_skill("Testing", "TestDummyBMSkillTesting");

        $this->assertEquals(1, $this->object->count_skill("Testing"));

        $die2->add_skill("Testing", "TestDummyBMSkillTesting");

        $this->assertEquals(2, $this->object->count_skill("Testing"));

        $die2->add_skill("Testing2", "TestDummyBMSkillTesting2");

        $this->assertEquals(1, $this->object->count_skill("Testing2"));

        // Nested containers

        $die3->add_skill("Testing", "TestDummyBMSkillTesting");
        $cont->add_thing($die3);
        $this->object->add_thing($cont);

        $this->assertEquals(3, $this->object->count_skill("Testing"));
        $this->assertEquals(1, $this->object->count_skill("Testing2"));

        // Skills on containers do count
        $cont->add_skill("Testing2", "TestDummyBMSkillTesting2");
        $this->assertEquals(3, $this->object->count_skill("Testing"));
        $this->assertEquals(2, $this->object->count_skill("Testing2"));

        // Add some more dice, the ones in the skilled container pick
        // up the skill
        $this->object->add_thing(new BMDie);
        $cont->add_thing(new BMDie);
        $cont->add_thing(new BMDie);
        $this->assertEquals(3, $cont->count_skill("Testing2"));
        $this->assertEquals(3, $this->object->count_skill("Testing"));
        $this->assertEquals(4, $this->object->count_skill("Testing2"));

        // But redundant skills only count once
        $this->object->add_skill("Testing2");
        $this->assertEquals(3, $this->object->count_skill("Testing"));
        $this->assertEquals(6, $this->object->count_skill("Testing2"));
    }

    /**
     * @covers BMContainer::remove_skill
     * @depends testAdd_skill
     * @depends testHas_skill
     */
    public function testRemove_skill()
    {
        // simple
        $this->object->add_skill("Testing");
        $this->assertTrue($this->object->remove_skill("Testing"));
        $this->assertFalse($this->object->has_skill("Testing"));

        // multiple skills
        $this->object->add_skill("Testing");
        $this->object->add_skill("Testing2");
        $this->assertTrue($this->object->remove_skill("Testing"));
        $this->assertFalse($this->object->has_skill("Testing"));
        $this->assertTrue($this->object->has_skill("Testing2"));

        // fail to remove non-existent skills
        $this->object->add_skill("Testing");
        $this->assertFalse($this->object->remove_skill("Testing3"));
        $this->assertTrue($this->object->has_skill("Testing"));
        $this->assertTrue($this->object->has_skill("Testing2"));
    }

    /**
     * @covers BMContainer::create_from_list
     * @requires testAdd_skill
     * @requires testAdd_thing
     */
    public function testCreate_from_list()
    {
        $cont = NULL;

        // flat test
        $cont = BMContainer::create_from_list(array(new BMDie));
        $this->assertNotNull($cont);
        $this->assertInstanceOf('BMContainer', $cont);
        $this->assertNotEmpty($cont->contents);
        $this->assertEquals(1, count($cont->contents));
        $this->assertInstanceOf('BMDie', $cont->contents[0]);

        $cont = NULL;

        $cont = BMContainer::create_from_list(array(new BMDie, new BMContainer, new TestDummyBMDieTesting));
        $this->assertNotNull($cont);
        $this->assertInstanceOf('BMContainer', $cont);
        $this->assertNotEmpty($cont->contents);
        $this->assertEquals(3, count($cont->contents));
        $this->assertInstanceOf('BMDie', $cont->contents[0]);
        $this->assertInstanceOf('BMContainer', $cont->contents[1]);
        $this->assertInstanceOf('TestDummyBMDieTesting', $cont->contents[2]);

        // Preservation of sub-container contents
        $cont2 = BMContainer::create_from_list(array(new BMContainer, new BMDie, $cont));
        $this->assertEquals(3, count($cont2->contents));
        $this->assertInstanceOf('BMContainer', $cont2->contents[0]);
        $this->assertInstanceOf('BMDie', $cont2->contents[1]);
        $this->assertInstanceOf('BMContainer', $cont2->contents[2]);
        $this->assertEquals(3, count($cont2->contents[2]->contents));
        $this->assertInstanceOf('BMDie', $cont2->contents[2]->contents[0]);
        $this->assertInstanceOf('BMContainer', $cont2->contents[2]->contents[1]);
        $this->assertInstanceOf('TestDummyBMDieTesting', $cont2->contents[2]->contents[2]);

        // Create new sub-containers from arrays
        $cont2 = BMContainer::create_from_list(array(new BMDie, array(new BMDie, new TestDummyBMDieTesting)));
        $this->assertEquals(2, count($cont2->contents));
        $this->assertEquals(2, count($cont2->contents[1]->contents));
        $this->assertInstanceOf('BMDie', $cont2->contents[0]);
        $this->assertInstanceOf('BMContainer', $cont2->contents[1]);
        $this->assertInstanceOf('BMDie', $cont2->contents[1]->contents[0]);
        $this->assertInstanceOf('TestDummyBMDieTesting', $cont2->contents[1]->contents[1]);

        $cont2 = BMContainer::create_from_list(array(new BMDie, array(new BMDie, array(new TestDummyBMDieTesting, new BMContainer))));
        $this->assertEquals(2, count($cont2->contents));
        $this->assertEquals(2, count($cont2->contents[1]->contents));
        $this->assertEquals(2, count($cont2->contents[1]->contents[1]->contents));
        $this->assertInstanceOf('BMDie', $cont2->contents[0]);
        $this->assertInstanceOf('BMContainer', $cont2->contents[1]);
        $this->assertInstanceOf('BMDie', $cont2->contents[1]->contents[0]);
        $this->assertInstanceOf('BMContainer', $cont2->contents[1]->contents[1]);
        $this->assertInstanceOf('TestDummyBMDieTesting', $cont2->contents[1]->contents[1]->contents[0]);
        $this->assertInstanceOf('BMContainer', $cont2->contents[1]->contents[1]->contents[1]);


        // skills
        $cont = BMContainer::create_from_list(array(new BMDie), array("Testing"));
        $this->assertTrue($cont->has_skill("Testing"));
        $this->assertFalse($cont->has_skill("Testing2"));

        $cont = BMContainer::create_from_list(array(new BMDie), array("Testing2"));
        $this->assertTrue($cont->has_skill("Testing2"));
        $this->assertFalse($cont->has_skill("Testing"));

        $cont = BMContainer::create_from_list(array(new BMDie), array("Testing2", "Testing"));
        $this->assertTrue($cont->has_skill("Testing"));
        $this->assertTrue($cont->has_skill("Testing2"));

        // Basic error modes -- rewrite to test with try/catch
        $fail = FALSE;

        try {
            $cont = NULL;

            $cont = BMContainer::create_from_list(array("thing"));
            $this->assertNull($cont);
        }
        catch (UnexpectedValueException $e) {
            $fail = TRUE;
        }
        $this->assertTrue($fail, "Bad contents didn't throw an exception.");

        $fail = FALSE;

        try {
            $cont = BMContainer::create_from_list(array(array("thing")));
            $this->assertNull($cont);
        }
        catch (UnexpectedValueException $e) {
            $fail = TRUE;
        }
        $this->assertTrue($fail, "Bad contents didn't throw an exception.");

        // more complex error
        $fail = FALSE;

        try {
            $cont2 = NULL;
            $cont2 = BMContainer::create_from_list(array(new BMDie, array(new TestDummyBMDieTesting, array(new BMContainer, "thing")), new BMContainer, new BMDie));
            $this->assertNull($cont2);
        }
        catch (UnexpectedValueException $e) {
            $fail = TRUE;
        }
        $this->assertTrue($fail, "Bad contents didn't throw an exception.");

    }

  /**
     * @covers BMContainer::activate
     * @depends testAdd_skill
     * @depends testAdd_thing
     */
    public function testActivate()
    {
        $game = new TestDummyGame;

        $cont1 = new BMContainer;
        $cont2 = new BMContainer;

        $dice = array();

        for ($i = 0; $i <7; $i++) {
            $tmp = new BMDie;
            $tmp->init($i);

            $dice[] = $tmp;
        }

        // Simple tests
        $this->object->ownerObject = $game;
        $this->object->activate("");

        $this->assertEquals(0, count($game->dice));

        $this->object->add_thing($dice[0]);

        $this->object->playerIdx = 'player';
        $this->object->activate();
        $this->assertEquals(1, count($game->dice));

        $d = $game->dice[0][1];
        $this->assertFalse($dice[0] === $d);
        $this->assertEquals("player", $game->dice[0][0]);
        $this->assertFalse($d->has_skill("Testing"));
        $this->assertFalse($d->has_skill("Testing2"));
        $this->assertEquals(0, $d->max);

        // more dice

        $game = new TestDummyGame;
        $this->object->ownerObject = $game;

        $dice[1]->add_skill("Testing", "TestDummyBMSkillTesting");
        $dice[2]->add_skill("Testing2", "TestDummyBMSkillTesting2");
        $dice[3]->add_skill("Testing", "TestDummyBMSkillTesting");
        $dice[3]->add_skill("Testing2", "TestDummyBMSkillTesting2");

        $this->object->add_thing($dice[1]);
        $this->object->add_thing($dice[2]);
        $this->object->add_thing($dice[3]);

        $this->object->activate("player");

        for ($i=0; $i<4; $i++) {
            $d = $game->dice[$i][1];

            // should be a new die
            foreach ($dice as $oldDie) {
                $this->assertFalse($d === $oldDie);
            }

            $this->assertEquals($i, $d->max);

            // skill combos
            if ($i == 1 || $i == 3) {
                $this->assertTrue($d->has_skill("Testing"));
            } else {
                $this->assertFalse($d->has_skill("Testing"));
            }
            if ($i == 2 || $i == 3) {
                $this->assertTrue($d->has_skill("Testing2"));
            } else {
                $this->assertFalse($d->has_skill("Testing2"));
            }
        }

        // Test skill addition

        $game = new TestDummyGame;

        $cont1->add_skill("Testing", "TestDummyBMSkillTesting");
        $cont2->add_skill("Testing2", "TestDummyBMSkillTesting2");

        $cont1->add_thing($dice[4]);
        $cont1->add_thing($dice[5]);
        $cont2->add_thing($dice[6]);

        $cont1->ownerObject = $game;
        $cont1->activate("player");

        foreach ($game->dice as $pair) {
            $d = $pair[1];

            $this->assertTrue($d->has_skill("Testing"));
            $this->assertFalse($d->has_skill("Testing2"));
        }

        // test activating nested containers
        $cont1->add_thing($cont2);
        $this->object->add_thing($cont1);

        $game = new TestDummyGame;
        $this->object->ownerObject = $game;

        $this->object->activate("player");

        $this->assertEquals(7, count($game->dice));

        for ($i=0; $i<7; $i++) {
            $d = $game->dice[$i][1];

            $this->assertEquals($i, $d->max);

            // skill combos
            if ($i == 1 || $i == 3 || $i >= 4) {
                $this->assertTrue($d->has_skill("Testing"));
            } else {
                $this->assertFalse($d->has_skill("Testing"));
            }
            if ($i == 2 || $i == 3 || $i == 6) {
                $this->assertTrue($d->has_skill("Testing2"));
            } else {
                $this->assertFalse($d->has_skill("Testing2"));
            }
        }
    }

    /**
     * @coversNothing
     * @depends testAdd_skill
     * @depends testAdd_thing
     */
    public function testInterfaceActivate()
    {
        $button = new BMButton;

        $this->object->ownerObject = $button;

        for ($dieIdx = 0; $dieIdx <= 6; $dieIdx++) {
            $dice[$dieIdx] = new BMDie;
            $dice[$dieIdx]->init($dieIdx);
        }

        // Simple tests
        $this->object->activate();
        $this->assertEquals(0, count($button->dieArray));

        $this->object->add_thing($dice[0]);
        $this->object->activate();
        $this->assertEquals(1, count($button->dieArray));

        $d = $button->dieArray[0];
        $this->assertFalse($dice[0] === $d);
        $this->assertFalse($d->has_skill("Testing"));
        $this->assertFalse($d->has_skill("Testing2"));
        $this->assertEquals(0, $d->max);

        // more dice
        $button = new BMButton;
        $this->object->ownerObject = $button;

        $dice[1]->add_skill("Testing", "TestDummyBMSkillTesting");
        $dice[2]->add_skill("Testing2", "TestDummyBMSkillTesting2");
        $dice[3]->add_skill("Testing", "TestDummyBMSkillTesting");
        $dice[3]->add_skill("Testing2", "TestDummyBMSkillTesting2");

        $this->object->add_thing($dice[1]);
        $this->object->add_thing($dice[2]);
        $this->object->add_thing($dice[3]);
        $this->object->activate();

        for ($i=0; $i<4; $i++) {
            $d = $button->dieArray[$i];

            // should be a new die
            foreach ($dice as $oldDie) {
                $this->assertFalse($d === $oldDie);
            }

            $this->assertEquals($i, $d->max);

            // skill combos
            if ($i == 1 || $i == 3) {
                $this->assertTrue($d->has_skill("Testing"));
            } else {
                $this->assertFalse($d->has_skill("Testing"));
            }
            if ($i == 2 || $i == 3) {
                $this->assertTrue($d->has_skill("Testing2"));
            } else {
                $this->assertFalse($d->has_skill("Testing2"));
            }
        }

        // Test skill addition
        $button = new BMButton;
        $this->object->ownerObject = $button;

        $cont1 = new BMContainer;
        $cont2 = new BMContainer;
        $cont1->ownerObject = $button;
        $cont1->add_skill("Testing", "TestDummyBMSkillTesting");
        $cont2->add_skill("Testing2", "TestDummyBMSkillTesting2");

        $cont1->add_thing($dice[4]);
        $cont1->add_thing($dice[5]);
        $cont2->add_thing($dice[6]);

        $cont1->activate();

        foreach ($button->dieArray as $die) {
            $d = $die;

            $this->assertTrue($d->has_skill("Testing"));
            $this->assertFalse($d->has_skill("Testing2"));
        }

        // test activating nested containers
        $cont1->add_thing($cont2);
        $this->object->add_thing($cont1);

        $button = new BMButton;
        $this->object->ownerObject = $button;

        $this->object->activate();

        $this->assertEquals(7, count($button->dieArray));

        for ($i=0; $i<7; $i++) {
            $d = $button->dieArray[$i];

            $this->assertEquals($i, $d->max);

            // skill combos
            if ($i == 1 || $i == 3 || $i >= 4) {
                $this->assertTrue($d->has_skill("Testing"));
            } else {
                $this->assertFalse($d->has_skill("Testing"));
            }
            if ($i == 2 || $i == 3 || $i == 6) {
                $this->assertTrue($d->has_skill("Testing2"));
            } else {
                $this->assertFalse($d->has_skill("Testing2"));
            }

        }
    }

    /**
     * @covers BMContainer::__clone
     * @requires testCreate_from_list
     */
    public function test__clone()
    {
        $list = array(new BMDie, new BMDie, new BMContainer);

        $c1 = BMContainer::create_from_list($list);

        $c2 = clone $c1;

        $this->assertFalse($c1 === $c2, "Basic clone failed");

        foreach ($c1->contents as $i => $thing) {
            $this->assertFalse($thing === $c2->contents[$i], "Sub-clone failed");
        }
    }
}
