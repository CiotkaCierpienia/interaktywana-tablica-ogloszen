#ifndef CARDREADER_H
#define CARDREADER_H
#pragma once
#include <wintypes.h> 
#include <winscard.h>
#include <stdlib.h>
#include <stdio.h>
#include <string>

using namespace std;

class CardReader
{
    public:
        CardReader();
        ~CardReader();
        unsigned int getIndex(void);
        string getImie(void);
        string getNazwisko(void);
        bool isCard(void);
    private:
        SCARDHANDLE hCard;
        SCARDCONTEXT hContext;
        SCARD_IO_REQUEST sRecvPci;
        SCARD_READERSTATE rgReaderStates;
        BYTE pbRecvBuffer[257];
        DWORD dwRecvLength, dwPref, dwReaders;
        LPSTR mszReaders;
        LPCSTR mszGroups; 
        long rv;
        bool is_present;
        string imie;
        string nazwisko;
        unsigned int indeks;
        void getPersonalData(void);
};

#endif // CARDREADER_H