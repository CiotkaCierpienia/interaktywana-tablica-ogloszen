#include "CardReader.h"
#include <stdlib.h>
#include <stdio.h>
#pragma comment(lib, "winscard.lib")   //windows only

CardReader::CardReader()
{
	try
	{
		mszReaders=NULL;
		dwReaders=SCARD_AUTOALLOCATE;
		dwRecvLength=264;
		dwRecvLength=sizeof(pbRecvBuffer);
		rv=SCardEstablishContext(SCARD_SCOPE_USER, NULL, NULL, &hContext);
		if(rv!=SCARD_S_SUCCESS)
		{
			printf("SCardEstablishContext "); 
			throw rv;
		}
		rv=SCardListReaders(hContext, NULL, (LPWSTR)&mszReaders, &dwReaders);
		if(rv!=SCARD_S_SUCCESS)
		{
			printf("SCardListReaders "); 
			throw rv;
		}
		rgReaderStates.szReader=mszReaders;
		rgReaderStates.dwCurrentState=SCARD_STATE_EMPTY;
	}
	catch (long e)
	{
		printf("error no. %lX\n",e);
	}
}
CardReader::~CardReader()
{
    if(mszReaders!=NULL)
		SCardFreeMemory(hContext, mszReaders);
	SCardReleaseContext(hContext);
}
unsigned int CardReader::getIndex(void)
{
	return 171021;
}
char* CardReader::getImie(void)
{
	return "Lukasz";
}
char* CardReader::getNazwisko(void)
{
	return "Glapinski";
}
bool CardReader::isCard(void)
{
	SCardGetStatusChange(hContext, 1000, &rgReaderStates, 1);
	if(rgReaderStates.dwEventState & SCARD_STATE_PRESENT)
        return true;
	return false;
}
void CardReader::connect(void)
{	//cos wysyla i cos odbiera ;] nieco to poprawic zeby bylo przejzysciej
	try
	{
		//TODO: Exclusive dawa³o b³¹d wspó³dzielenia na windowsie, sprawdziæ pod debianem
		//dodaæ obs³ugê b³êdów wyci¹gniêcia karty :P
		rv=SCardConnect(hContext, rgReaderStates.szReader, SCARD_SHARE_SHARED, SCARD_PROTOCOL_T0 | SCARD_PROTOCOL_T1, &hCard, &dwPref);
		if(rv!=SCARD_S_SUCCESS)
		{
			printf("SCardConnect ");
			throw rv;
		}
		switch(dwPref)
		{
			case SCARD_PROTOCOL_T0:	sRecvPci=*SCARD_PCI_T0;
									break;
			case SCARD_PROTOCOL_T1:	sRecvPci=*SCARD_PCI_T1;
									break;
		}
		SCardBeginTransaction(hCard);
		BYTE pbSendBuffer[]={0x00, 0xA4, 0x04, 0x00, 0x0A, 0xA0, 0x00, 0x00, 0x00, 0x62, 0x03, 0x01, 0x0C, 0x06, 0x01 };
		rv=SCardTransmit(hCard, &sRecvPci, pbSendBuffer, sizeof(pbSendBuffer), NULL, pbRecvBuffer, &dwRecvLength);
		if(rv!=SCARD_S_SUCCESS)
		{
			printf("SCardTransmit ");
			throw rv;
		}
		printf("response: ");
		for(unsigned i=0; i<dwRecvLength; i++)
			printf("%02X ", pbRecvBuffer[i]);
		printf("\n");
		dwRecvLength = sizeof(pbRecvBuffer);
		SCardEndTransaction(hCard, SCARD_LEAVE_CARD);
		SCardDisconnect(hCard, SCARD_UNPOWER_CARD);
	}
	catch(long e)
	{
		printf("error no. %lX\n",e);
	}
}