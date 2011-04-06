#ifndef CARDREADER_H
#define CARDREADER_H
#pragma once
//#include <wintypes.h>		//linux only
#include <winscard.h>

class CardReader
{
    public:
        CardReader();
        ~CardReader();
        unsigned int getIndex(void);
        char* getImie(void);
        char* getNazwisko(void);
        bool isCard(void);
		void connect(void);
    protected:
    private:
        SCARDHANDLE hCard;
        SCARDCONTEXT hContext;
        SCARD_IO_REQUEST sRecvPci;
        SCARD_READERSTATE rgReaderStates;
		BYTE pbRecvBuffer[10];
        DWORD dwRecvLength, dwPref, dwReaders;
        LPWSTR mszReaders;	//usun¹æ W pod linuxem
        LPCWSTR mszGroups;	//j.w.
        long rv;
};

#endif // CARDREADER_H