#ifndef CARDREADER_H
#define CARDREADER_H
#include <wintypes.h>
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
    protected:
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
};

#endif // CARDREADER_H