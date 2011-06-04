#include "stdafx.h"
#include "CardReader.h"

int main()
{
	CardReader cr;
	while(1)
	{
		if(cr.isCard())
		{
			printf("Jest\n");
			cr.connect();
			getchar();
		}
		else
			printf("Nie ma\n");
		Sleep(200);
	}
	return 0;
}

