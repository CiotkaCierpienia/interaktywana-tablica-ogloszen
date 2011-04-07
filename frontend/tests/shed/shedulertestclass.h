/*
 * File:   shedulertestclass.h
 * Author: krzysiek
 *
 * Created on Apr 6, 2011, 10:56:48 PM
 */

#ifndef SHEDULERTESTCLASS_H
#define	SHEDULERTESTCLASS_H

#include <cppunit/extensions/HelperMacros.h>

class shedulertestclass : public CPPUNIT_NS::TestFixture {
	CPPUNIT_TEST_SUITE(shedulertestclass);

	CPPUNIT_TEST(testSheduler_01prosty);
	CPPUNIT_TEST(testSheduler_02dluzszy);
	CPPUNIT_TEST(testSheduler_03wielokrotnieZlozona);

	CPPUNIT_TEST_SUITE_END();

public:
	shedulertestclass(){}
	virtual ~shedulertestclass(){}
	void setUp(){}
	void tearDown(){}

private:
	void testSheduler_01prosty();
	void testSheduler_02dluzszy();
	void testSheduler_03wielokrotnieZlozona();

};

#endif	/* SHEDULERTESTCLASS_H */

