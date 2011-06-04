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
        bool isCard(void);
        unsigned int getIndex(void);
        string getImie(void);
        string getNazwisko(void);
    private:
        SCARDHANDLE hCard;                          //uchwyt karty
        SCARDCONTEXT hContext;                      //kontekst czytnika
        SCARD_IO_REQUEST sRecvPci;                  //typ żądania
        SCARD_READERSTATE rgReaderStates;           //stany czytnika
        BYTE pbRecvBuffer[257];                     //bufor przychodzący
        DWORD dwRecvLength, dwPref, dwReaders;
        LPSTR mszReaders;                           //nazwa czytnika
        string imie;                                //bufor przechowujący imię
        string nazwisko;                            //bufor przechowujący nazwisko
        long rv;                                    //zmienna kontrolna
        bool is_present;                            //określa dostępność karty
        unsigned int indeks;                        //bufor przechowujący nr indeksu
        void getPersonalData(void);                 //pobiera dane do bufora
};

#endif // CARDREADER_H

