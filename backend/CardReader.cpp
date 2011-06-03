#include "CardReader.h"

CardReader::CardReader()
{
        try
        {
                is_present=false;   //karta wyciągnięta
                indeks=0;           //czyścimy bufor
                imie="brak";
                nazwisko="brak";
                mszReaders=0;
                dwReaders=SCARD_AUTOALLOCATE;       //automatyczna alokacja pamięci dla danych czytnika
                dwRecvLength=sizeof(pbRecvBuffer);  //długość danych przychodzących
                rv=SCardEstablishContext(SCARD_SCOPE_USER, 0, 0, &hContext);    //ustawiamy kontekst
                if(rv!=SCARD_S_SUCCESS)             //jeżeli operacja się nie powiodła wyrzuca wyjątek
                {
                        printf("SCardEstablishContext ");
                        throw rv;
                }
                rv=SCardListReaders(hContext, 0, (LPSTR)&mszReaders, &dwReaders);   //listuje podłączone czytniki
                if(rv!=SCARD_S_SUCCESS)
                {
                        printf("SCardListReaders ");
                        throw rv;
                }
                rgReaderStates.szReader=mszReaders;                 //ustawia strukturę stanów czytnika
                rgReaderStates.dwCurrentState=SCARD_STATE_EMPTY;    //status na pusty
        }
        catch (long e)
        {
                printf("error: %s\n",pcsc_stringify_error(e));
        }
}
CardReader::~CardReader()
{
        if(mszReaders!=NULL)                            //zwalnia zaalokowaną pamięć
                SCardFreeMemory(hContext, mszReaders);
        SCardReleaseContext(hContext);                  //zwalnia kontekst
}
string CardReader::getImie(void)
{
        if(!is_present)             //jeśli karta była wyciągnięta
                getPersonalData();  //pobiera dane do bufora
        return imie;
}
string CardReader::getNazwisko(void)
{
        if(!is_present)
                getPersonalData();
        return nazwisko;
}
unsigned int CardReader::getIndex(void)
{
        if(!is_present)
                getPersonalData();
        return indeks;
}
bool CardReader::isCard(void)
{
        SCardGetStatusChange(hContext, 1000, &rgReaderStates, 1);   //wykrywa zmianę stanu czytnika
        if(rgReaderStates.dwEventState & SCARD_STATE_PRESENT)       //jeżeli karta jest w czytniku
                return true;
        else
                is_present=false;                                   //karty nie ma w czytniku
        return false;
}
void CardReader::getPersonalData(void)
{
        //komendy wybrania aplikacji i pobrania danych
        BYTE select_mf_cmd[]={0x00, 0xA4, 0x00, 0x0C, 0x02, 0x3F, 0x00};
        BYTE select_df_cmd[]={0x00, 0xA4, 0x04, 0x00, 0x07, 0xD6, 0x16, 0x00, 0x00, 0x30, 0x01, 0x01};
        BYTE select_ef_cmd[]={0x00, 0xA4, 0x00, 0x0C, 0x02, 0x00, 0x02};
        BYTE read_cmd[]={0x00, 0xB0, 0x00, 0x00, 0xFF};
        int j=0;
        try
        {
                rv=SCardConnect(hContext, rgReaderStates.szReader, SCARD_SHARE_EXCLUSIVE, SCARD_PROTOCOL_T0, &hCard, &dwPref);  //łączy z kartą
                if(rv!=SCARD_S_SUCCESS)
                {
                        printf("SCardConnect ");
                        is_present=false;                               //karty nie ma
                        rgReaderStates.dwEventState&=SCARD_STATE_EMPTY; //status na pusty
                        indeks=0;                                       //czyszczenie buforów
                        imie="";
                        nazwisko="";
                        throw rv;
                }
                dwRecvLength=sizeof(pbRecvBuffer);
                rv=SCardTransmit(hCard, SCARD_PCI_T0, select_mf_cmd, sizeof(select_mf_cmd), 0, pbRecvBuffer, &dwRecvLength);    //przesłanie komendy
                if(rv!=SCARD_S_SUCCESS)
                {
                        SCardDisconnect(hCard, SCARD_UNPOWER_CARD); //rozłączamy z kartą
                        is_present=false;
                        rgReaderStates.dwEventState&=SCARD_STATE_EMPTY;
                        indeks=0;
                        imie="";
                        nazwisko="";
                        printf("SCardTransmit ");
                        throw rv;
                }
                dwRecvLength=sizeof(pbRecvBuffer);
                rv=SCardTransmit(hCard, SCARD_PCI_T0, select_df_cmd, sizeof(select_df_cmd), 0, pbRecvBuffer, &dwRecvLength);
                if(rv!=SCARD_S_SUCCESS)
                {
                        SCardDisconnect(hCard, SCARD_UNPOWER_CARD);
                        is_present=false;
                        rgReaderStates.dwEventState&=SCARD_STATE_EMPTY;
                        indeks=0;
                        imie="";
                        nazwisko="";
                        printf("SCardTransmit ");
                        throw rv;
                }
                dwRecvLength=sizeof(pbRecvBuffer);
                rv=SCardTransmit(hCard, SCARD_PCI_T0, select_ef_cmd, sizeof(select_ef_cmd), 0, pbRecvBuffer, &dwRecvLength);
                if(rv!=SCARD_S_SUCCESS)
                {
                        SCardDisconnect(hCard, SCARD_UNPOWER_CARD);
                        is_present=false;
                        rgReaderStates.dwEventState&=SCARD_STATE_EMPTY;
                        indeks=0;
                        imie="";
                        nazwisko="";
                        printf("SCardTransmit ");
                        throw rv;
                }
                dwRecvLength=sizeof(pbRecvBuffer);
                rv=SCardTransmit(hCard, SCARD_PCI_T0, read_cmd, sizeof(read_cmd), 0, pbRecvBuffer, &dwRecvLength);
                if(rv!=SCARD_S_SUCCESS)
                {
                        SCardDisconnect(hCard, SCARD_UNPOWER_CARD);
                        is_present=false;
                        rgReaderStates.dwEventState&=SCARD_STATE_EMPTY;
                        indeks=0;
                        imie="";
                        nazwisko="";
                        printf("SCardTransmit ");
                        throw rv;
                }
                string result[50];                              //przechowuje sformatowany wynik
                bool last_byte_control=false, hex_0x0C=false;   //zmienne kontrolne
                for(unsigned int i=0;i<dwRecvLength-2;i++)      //parsuje odebrane dane do stringa
                {
                        int code=pbRecvBuffer[i];
                        if(code>0)
                        {
                                char c=(char)pbRecvBuffer[i];
                                if(code>31 && code<256)
                                {
                                        if(hex_0x0C==false)
                                        {
                                                result[j]+=c;
                                        }
                                }
                                }
                                if(hex_0x0C==true)
                                {
                                        hex_0x0C=false;
                                }
                                if(((code==0x0C) || (code==0x13) || (code==0x18)) && (last_byte_control==false))
                                {
                                        last_byte_control=true;
                                        j++;
                                        hex_0x0C=true;
                                }
                                else
                                        last_byte_control=false;
                        }
                }
                SCardDisconnect(hCard, SCARD_UNPOWER_CARD);
                is_present=true;    //karta w czytniku
                imie=result[4];     //ustawianie bufora
                nazwisko=result[3].substr(0,result[3].length()-1);
                indeks=atoi(result[6].c_str());
        }
        catch(long e)
        {
                printf("error: %s\n",pcsc_stringify_error(e));
        }
}
