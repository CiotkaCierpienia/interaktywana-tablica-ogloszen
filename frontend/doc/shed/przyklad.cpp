/* 
 * File:   main.cpp
 * Author: krzysiek
 *
 * Created on April 6, 2011, 10:43 PM
 */

#include <cstdio>
#include <string>
#include "shed.h"
using namespace std;

#define ILOSC_KOMUNIKATOW 10


/*
 * 
 */
int main() {

	int priorytety[ILOSC_KOMUNIKATOW];
	string tresc[ILOSC_KOMUNIKATOW];

	//stworzenie listy komunikatow
	priorytety[0]=2;	tresc[0]="Komuniat sredniowazny 1";
	priorytety[1]=1;	tresc[1]="Komuniat wazny 1";
	priorytety[2]=3;	tresc[2]="Komuniat malowazny 1";
	priorytety[3]=2;	tresc[3]="Komuniat sredniowazny 2";

	priorytety[4]=2;	tresc[4]="Komuniat sredniowazny 3";
	priorytety[5]=3;	tresc[5]="Komuniat malowazny 2";
	priorytety[6]=3;	tresc[6]="Komuniat malowazny 3";
	priorytety[7]=1;	tresc[7]="Komuniat wazny 2";

	priorytety[8]=2;	tresc[8]="Komuniat sredniowazny 4";
	priorytety[9]=1;	tresc[9]="Komuniat wazny 3";

	list<int> kolejka= sheduler(priorytety, ILOSC_KOMUNIKATOW);

	list<int>::iterator i;
	for (i=kolejka.begin(); i!=kolejka.end(); ++i) {
		printf("%s\n", tresc[*i].c_str() );
	}


	return 0;
}

