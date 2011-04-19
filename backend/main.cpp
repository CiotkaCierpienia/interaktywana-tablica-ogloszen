#include <QtGui/QApplication>
#include "widget.h"
#include "Mythread.h"
#include <QTimer>
#include "app.h"




int main(int argc, char *argv[])
{


    QApplication a(argc, argv);
    Widget w,w2;





    QSqlDatabase db = QSqlDatabase::addDatabase("QMYSQL3");
    db.setDatabaseName( "mydb" ) ;
    db.setHostName( "localhost" ) ;
    db.setUserName( "root" ) ;
    db.setPassword( "root" ) ;
    //MyThread as(&w,&db);


    w.setWindowTitle("Display");
    QPalette pal = w.palette();
    pal.setColor(w.backgroundRole(),Qt::black);
    w.setPalette(pal);
    w.showFullScreen();
    w.setup();
    w.ustaw_pokoj(121);
    w.polacz(db);
    w.konsultacje("Biernat");


    w.wyniki();
    QTimer::singleShot(0,&w,SLOT(ogloszenia()));
    QTimer::singleShot(0,&w,SLOT(readCard()));






//        }
//        else
//        {

//            w.test();
//            // w.wyniki(karta);
//        }








     //QTimer::singleShot(1000, &a, SLOT(oglaszenia));


 //   w.rozlacz(db);




   /* if(karta.isCard()){
        w.readCard(karta);
    w.test();}


        else*/
            //w.polacz();







    return a.exec();
}





