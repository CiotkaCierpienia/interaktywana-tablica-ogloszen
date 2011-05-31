#include <QtGui/QApplication>
#include "widget.h"
#include <QTimer>
#include <QTextCodec>





int main(int argc, char *argv[])
{


    QApplication a(argc, argv);
    Widget w;

    // ustawienia kodownaia znaków na UTF-8
    QTextCodec::setCodecForCStrings(QTextCodec::codecForName ("UTF-8"));




    QSqlDatabase db = QSqlDatabase::addDatabase("QMYSQL3");
    db.setDatabaseName( "projekt" ) ;
    db.setHostName( "localhost" ) ;
    db.setUserName( "root" ) ;
    db.setPassword( "admin1" ) ;
    //MyThread as(&w,&db);


    w.setWindowTitle("Display");
    QPalette pal = w.palette();
    pal.setColor(w.backgroundRole(),Qt::black);
    w.setPalette(pal);     // ustawienie domyślnego koloru tła ( czarny )
    w.showFullScreen();    // opcja FullScreen
    w.setup();            //inicializacja widgetu
    w.ustaw_pokoj(121);   // ustawienie numeru pokoju
    w.polacz(db);        //połaczenie z bazą
  


    w.wyniki();       //wyświetlenie dostępnych wyników
    
    QTimer::singleShot(0,&w,SLOT(ogloszenia()));     //wyświetlanie ogłoszeń dostępnych w bazie dla studentów
    QTimer::singleShot(0,&w,SLOT(konsultacje()));    // wyswietlanie konsultacji osób przypisanych do pokoju
    QTimer::singleShot(0,&w,SLOT(readCard()));       // odczytywanie legitymacji studenckiej, gdy taka sie pojawi





    


 //   w.rozlacz(db);


  
    return a.exec();
}





