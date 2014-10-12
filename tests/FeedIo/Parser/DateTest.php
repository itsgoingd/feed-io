<?php
/*
 * This file is part of the feed-io package.
 *
 * (c) Alexandre Debril <alex.debril@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FeedIo\Parser;


class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Timezone used to test a timezone switch.
     * The Troll Research Center is the only place in the world where the testSetTimezone() test will fail,
     * I hope it won't bother anyone
     */
    const ALTERNATE_TIMEZONE = 'Antarctica/Troll';

    /**
     * @var \FeedIo\Parser\DateTimeBuilder
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new DateTimeBuilder();
    }

    public function testGetTimezone()
    {
        $timezone = $this->object->getTimezone();
        $this->assertEquals(date_default_timezone_get(), $timezone->getName());
    }

    public function testSetDateFormats()
    {
        $formats = array(\DateTime::ATOM);
        $this->object->setDateFormats($formats);
        $this->assertAttributeEquals($formats, 'dateFormats', $this->object);
    }

    public function testGuessDateFormat()
    {
        $formats = array(\DateTime::ATOM, \DateTime::RFC1036);
        $this->object->setDateFormats($formats);
        $date = new \DateTime();
        $format = $this->object->guessDateFormat($date->format(\DateTime::ATOM));
        $this->assertEquals(\DateTime::ATOM, $format);
    }


    public function testDontGuessDateFormat()
    {
        $this->object->addDateFormat(\DateTime::ATOM);
        $this->assertFalse($this->object->guessDateFormat('foo'));
    }

    public function testConvertDateFormat()
    {
        $formats = array(\DateTime::ATOM, \DateTime::RFC1036);
        $this->object->setDateFormats($formats);

        $date = new \DateTime();
        $this->assertEquals($date, $this->object->convertToDateTime($date->format(\DateTime::ATOM)));
        $this->assertEquals(\DateTime::ATOM, $this->object->getLastGuessedFormat());
        $this->assertEquals($date, $this->object->convertToDateTime($date->format(\DateTime::RFC1036)));
        $this->assertEquals(\DateTime::RFC1036, $this->object->getLastGuessedFormat());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDontConvertDateFormat()
    {
        $this->object->addDateFormat(\DateTime::ATOM);
        $this->object->convertToDateTime('foo');
    }

    public function testSetTimezone()
    {
        $this->object->setTimezone(new \DateTimeZone(self::ALTERNATE_TIMEZONE));
        $this->assertEquals(self::ALTERNATE_TIMEZONE, $this->object->getTimezone()->getName());

        $this->object->addDateFormat(\DateTime::ATOM);
        $date = new \DateTime();
        $return = $this->object->convertToDateTime($date->format(\DateTime::ATOM));

        $this->assertEquals(self::ALTERNATE_TIMEZONE, $return->getTimezone()->getName());
    }
}
