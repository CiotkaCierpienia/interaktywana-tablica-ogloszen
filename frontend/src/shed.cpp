/*
 * File:   shed.cpp
 * Author: Krzysztof Grzywocz <171113@student.pwr.wroc.pl>
 *
 * Created on April 6, 2011, 10:46 PM
 */
#include <vector>
#include <algorithm>
#include "shed.h"
using namespace std;

/**
 * Funcja pobiera tablice priorytetow, a zwraca liste numerow komunikatow
 * w kolejnosci do wyswietlania. Numery sie powtarzaja.
 */
list<int> sheduler(int priorytety[], int n)
{
	list<int> l, lpom;
	pair<int, int> p;
	vector< pair<int, int> > v;

	if(n<=0)
		return l;

	//wrzucenie listy prirytetow do vektora par (priorytet, nr komunikatu)
	for (int i = 0; i < n; i++) {
		p.first = priorytety[i];
		p.second = i;
		v.push_back(p);
	}

	stable_sort(v.begin(), v.end());

	//wlasciwa ukladanka
	int bierzacyPrio=v[0].first;
	vector< pair<int, int> >::iterator i;
	for ( i = v.begin(); i != v.end(); ++i) {
		if(i->first != bierzacyPrio){
			lpom=l;
			l.clear();
			bierzacyPrio= i->first;
		}
		l.insert(l.end(), lpom.begin(), lpom.end());
		l.push_back(i->second);
	}

	return l;
}