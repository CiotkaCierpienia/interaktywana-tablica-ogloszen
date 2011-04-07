/*
 * File:   shedulertestclass.cpp
 * Author: krzysiek
 *
 * Created on Apr 6, 2011, 10:56:48 PM
 */

#include "shedulertestclass.h"
#include "inc/shed.h"
using namespace std;

void wypisz_liste(list<int> l)
{
	list<int>::iterator i;
	for (i=l.begin(); i!=l.end(); ++i) {
		fprintf(stderr, "%d ", *i);
	}

}


CPPUNIT_TEST_SUITE_REGISTRATION(shedulertestclass);


void shedulertestclass::testSheduler_01prosty() {
	int priorytety[2];
	int n=sizeof(priorytety)/sizeof(priorytety[0]);

	//stworzenie listy
	priorytety[0]=2;
	priorytety[1]=1;

	//wywolanie
	std::list<int> result=sheduler(priorytety, n);

	//sprawdzenie listy
	list<int>::iterator i=result.begin();
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 0);
}

void shedulertestclass::testSheduler_02dluzszy() {
	int priorytety[6];
	int n=sizeof(priorytety)/sizeof(priorytety[0]);

	//stworzenie listy
	priorytety[0]=2;
	priorytety[1]=1;
	priorytety[2]=1;
	priorytety[3]=1;
	priorytety[4]=2;
	priorytety[5]=1;

	//wywolanie
	std::list<int> result=sheduler(priorytety, n);

	//sprawdzenie listy
	list<int>::iterator i=result.begin();
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 2);
	CPPUNIT_ASSERT(*i++ == 3);
	CPPUNIT_ASSERT(*i++ == 5);
		CPPUNIT_ASSERT(*i++ == 0);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 2);
	CPPUNIT_ASSERT(*i++ == 3);
	CPPUNIT_ASSERT(*i++ == 5);
		CPPUNIT_ASSERT(*i++ == 4);
	
}


void shedulertestclass::testSheduler_03wielokrotnieZlozona() {
	int priorytety[10];
	int n=sizeof(priorytety)/sizeof(priorytety[0]);

	//stworzenie listy
	priorytety[0]=2;
	priorytety[1]=1;
	priorytety[2]=3;
	priorytety[3]=2;

	priorytety[4]=2;
	priorytety[5]=3;
	priorytety[6]=3;
	priorytety[7]=1;

	priorytety[8]=2;
	priorytety[9]=1;

	//wywolanie
	std::list<int> result=sheduler(priorytety, n);

	wypisz_liste(result);

	//sprawdzenie listy
	list<int>::iterator i=result.begin();
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 0);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 3);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 4);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 8);
			CPPUNIT_ASSERT(*i++ == 2);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 0);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 3);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 4);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 8);
			CPPUNIT_ASSERT(*i++ == 5);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 0);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 3);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 4);
	CPPUNIT_ASSERT(*i++ == 1);
	CPPUNIT_ASSERT(*i++ == 7);
	CPPUNIT_ASSERT(*i++ == 9);
		CPPUNIT_ASSERT(*i++ == 8);
			CPPUNIT_ASSERT(*i++ == 6);
			

}