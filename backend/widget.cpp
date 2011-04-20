#include "widget.h"
#include "ui_widget.h"
#include "QTime"
#include <stdio.h>
#include <QTimer>



using namespace std;



Widget::Widget(QWidget *parent) :
    QWidget(parent),
    ui(new Ui::Widget)
{


    ui->setupUi(this);

}

Widget::~Widget()
{
        delete ui;
}

extern QString gDateVariable;

void Widget::ogloszenia()
{
    static int init=0;
    static list<int>::iterator k;
    static std::list<int> kolejka;

    static int i=0;
    static int priorytet[50];
    static QString oglosz[50];
    static QTime deadline[50];

    if(init==0 || k==kolejka.end())
    {
        init=1;
        //zapelnij_kolejke(&kolejka);
        QSqlQuery query1("SELECT * FROM ogloszenia");
        while (query1.next()) {
            //QString indeks = query.value(0).toString();//toString();

            oglosz[i] = query1.value(2).toString();//toString();
            deadline[i] = query1.value(4).toTime();
            priorytet[i++]= query1.value(5).toInt();//.toString();
        }


        kolejka= sheduler(priorytet, i);
        k=kolejka.begin();
    }

    int nr=*k;
    int czasy_wyswietlania= oglosz[nr].length()*200;
    ui->ogloszenia->setText("\n\n"+oglosz[nr]);
    k++;

    QTimer::singleShot(czasy_wyswietlania,this,SLOT(ogloszenia()));

}
list<int> Widget::sheduler(int priorytety[], int n)
{
    list<int> l, lpom;
    pair<int, int> p;
    vector< std::pair<int, int> > v;


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
void Widget::wyniki()
 {
    int indeks= karta.getIndex();
    QString ocena,info,data;
    char indekss[20];
    sprintf(indekss,"%d",indeks);

    QString zpytanie="SELECT * FROM oceny "\
                     "WHERE ID_asoc_stud_grupa = "\
                     "(SELECT ID_asoc_stud_grupa FROM asoc_stud_grupa "\
                     "WHERE indeks = ";
    zpytanie+=indekss;
    zpytanie+=")";

    QSqlQuery query(zpytanie);
         while (query.next()) {
               ocena = query.value(4).toString();//toString();
              info= query.value(5).toString();
              data= query.value(6).toString();
       }
         ui->wyniki->append("Ocena : "+ocena+"\nInfo : "+info+"\n Data : "+data);
         QSqlQuery query1("SELECT * FROM ogloszenia WHERE ID_ogloszenia = (SELECT ID_ogloszenia FROM asoc_ogl_stud WHERE indeks =" +(QString) indeks + ")");
              while (query1.next()) {
                    info = query1.value(2).toString();//toString();
                   data= query1.value(4).toString();
            }
              ui->wyniki->append("\nOgloszenie: "+info+"\n Data : "+data);

}

void Widget::polacz( QSqlDatabase baza)
{

    if ( !baza.open() ) {
        qDebug( "Failed opening database dbToConnectTo\nReason:" ) ;
        qDebug( "db.lastError().driverText()" ) ;
        qDebug( "db.lastError().databaseText()" ) ;
        exit ( 1 ) ;
    }
}
void Widget::rozlacz(  QSqlDatabase baza)
{
    baza.close();

}

void Widget::readCard()
{
    if(karta.isCard()){

    QPalette pal = palette();
    int indeks= karta.getIndex();
    QString ocena,info,data;
    char indekss[20];
    sprintf(indekss,"%d",indeks);
    pal.setColor(QPalette::Active,static_cast<QPalette::ColorRole>(9),QColor (Qt::green));
    ui->legitymacja->setPalette(pal);
    //ui->legitymacja->setVisible(true);
    //ui->legitymacja->showMaximized();
    //ui->legitymacja->setHidden(false);
    //if(ui->pushButton->isChecked())    ui->legitymacja->setHidden(true);
    QString zpytanie="SELECT * FROM oceny "\
                     "WHERE ID_asoc_stud_grupa = "\
                     "(SELECT ID_asoc_stud_grupa FROM asoc_stud_grupa "\
                     "WHERE indeks = ";
    zpytanie+=indekss;
    zpytanie+=")";

    QSqlQuery query(zpytanie);
         while (query.next()) {
               ocena = query.value(4).toString();//toString();
              info= query.value(5).toString();
              data= query.value(6).toString();
       }

     ui->legitymacja->setHidden(false);
  //  ui->legitymacja->setText(karta.getImie()+karta.getNazwisko()+"\nOcena:"+ocena+"\nInfo :"+info+"\nData :"+data);

    QTimer::singleShot(100,this,SLOT(readCard()));
}
    else
    {
        ui->legitymacja->setVisible(false);
        QTimer::singleShot(100,this,SLOT(readCard()));
    }
}
void Widget::test()
{
    //ui->legitymacja->setHidden(true);

    ui->legitymacja->setVisible(false);
  // ui->legitymacja->append("Imie : "+ l->getImie());
   //ui->legitymacja->append("\nNazwisko : "+l->getNazwisko());
  // ui->legitymacja->append("\nIndeks : "+l->getIndex());


}

void Widget::setup(){

    QPalette pal,pal1,wh,blue= palette();
    pal.setColor(QPalette::Active,static_cast<QPalette::ColorRole>(9),QColor (Qt::green));
    pal1.setColor(QPalette::Active,static_cast<QPalette::ColorRole>(9),QColor(204,255,255));
    wh.setColor(QPalette::Active,static_cast<QPalette::ColorRole>(9),QColor (Qt::white));
    blue.setColor(QPalette::Active,static_cast<QPalette::ColorRole>(9),QColor (99,184,255));
    ui->ogloszenia->setFrameStyle(QFrame::StyledPanel);
    ui->wyniki->setFrameStyle(QFrame::StyledPanel);
    ui->konsultacje->setFrameStyle(QFrame::StyledPanel);
    ui->ogloszenia->setStyleSheet("background-image: url(/home/bla/display/tlo/ogl.png)");
    ui->wyniki->setStyleSheet("background-image: url(/home/bla/display/tlo/blue_dwn.png)");
    ui->konsultacje->setStyleSheet("background-image: url(/home/bla/display/tlo/blue.png)");


    //ui->pokoj->setPalette(wh);
    //ui->textEdit->setDisabled(false);

    ui->ogloszenia->setFontItalic(true);


}

void Widget::ustaw_pokoj(int nr)
{

    ui->pokoj->setDigitCount(3);
    ui->pokoj->setDecMode();
    ui->pokoj->font();
    ui->pokoj->display(nr);
  }
void Widget::konsultacje(QString nazwisko)
{
    QString imie,stopien,status,email,tel ;
    QString prowadzacy,dzien,od_,do_;



QSqlQuery query2("SELECT * FROM prowadzacy WHERE nazwisko LIKE '"+nazwisko+"'");
query2.first();
     prowadzacy = query2.value(0).toString();//toString();
     imie = query2.value(1).toString();//toString();
     stopien = query2.value(3).toString();
      status = query2.value(4).toString();//toString();
      email = query2.value(5).toString();
      tel = query2.value(6).toString();

    ui->konsultacje->append("\n\nProwadzacy : "+stopien+" "+imie + " "+nazwisko+"\nemail : "+ email + "\n tel. "+tel+"\n");


    QSqlQuery query("SELECT * FROM konsultacje WHERE ID_osoby = "+ prowadzacy);
         while (query.next()) {
               dzien = query.value(2).toString();//toString();
              od_ = query.value(3).toString();
              do_ = query.value(4).toString();
              ui->konsultacje->append( dzien + " od "+ od_ + " do "+ do_);
        }

   }

    //ui->konsultacje->append("Prowadzacy : "+stopien+" "+imie + " "+nazwisko+"\nemail : "+ email + "\n tel. "+tel+"\n");


void Widget::kolejka()
{

            int priorytety[10];
            QString tresc[10];


            //stworzenie listy komunikatow
            priorytety[0]=2;        tresc[0]="Komuniat sredniowazny 1";
            priorytety[1]=1;        tresc[1]="Komuniat wazny 1";
            priorytety[2]=3;        tresc[2]="Komuniat malowazny 1";
            priorytety[3]=2;        tresc[3]="Komuniat sredniowazny 2";


            priorytety[4]=2;        tresc[4]="Komuniat sredniowazny 3";
            priorytety[5]=3;        tresc[5]="Komuniat malowazny 2";
            priorytety[6]=3;        tresc[6]="Komuniat malowazny 3";
            priorytety[7]=1;        tresc[7]="Komuniat wazny 2";


            priorytety[8]=2;        tresc[8]="Komuniat sredniowazny 4";
            priorytety[9]=1;        tresc[9]="Komuniat wazny 3";


            std::list<int> kolejka= sheduler(priorytety, 10);


            std::list<int>::iterator i;
            for (i=kolejka.begin(); i!=kolejka.end(); ++i) {
                    ui->ogloszenia->setText(tresc[*i]);
            }





    }


