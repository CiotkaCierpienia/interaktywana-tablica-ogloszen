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
        QSqlQuery query1("SELECT ogloszenie,data_wygasniecia,priorytet FROM ogloszenia");
        while (query1.next()) {
            //QString indeks = query.value(0).toString();//toString();

            oglosz[i] = query1.value(0).toString();//toString();
            deadline[i] = query1.value(1).toTime();
            priorytet[i++]= query1.value(2).toInt();//.toString();
        }


        kolejka= sheduler(priorytet, i);
        k=kolejka.begin();
    }

    int nr= *k;
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

    QString nazwa_typu,przedmiot,kod_grupy;

    QString zpytanie="SELECT DISTINCTROW nazwa_typu, kod_grupy, przedmiot/"
                     "FROM typy_ocen/"
                     "NATURAL JOIN oceny/"
                     "NATURAL JOIN asoc_stud_grupa/"
                     "NATURAL JOIN grupa/"
                     "NATURAL JOIN przedmioty/"
                     "WHERE oceny.ID_typu IS NOT NULL";


    QSqlQuery query(zpytanie);
         while (query.next()) {
               nazwa_typu = query.value(0).toString();//toString();
              kod_grupy= query.value(1).toString();
              przedmiot= query.value(2).toString();
       }
         ui->wyniki->setText("\n\nDostępne są wyniki z "+nazwa_typu+" dla grupy o kodzie "+kod_grupy+
                            " z predzmiotu "+ przedmiot+"\n");

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
    unsigned int indeks= karta.getIndex();
    QString ocena,info,data;
    char indekss[20];
    sprintf(indekss,"%d",indeks);
    pal.setColor(QPalette::Active,static_cast<QPalette::ColorRole>(9),QColor (Qt::green));
    ui->legitymacja->setPalette(pal);
    //ui->legitymacja->setVisible(true);
    //ui->legitymacja->showMaximized();
    //ui->legitymacja->setHidden(false);
    //if(ui->pushButton->isChecked())    ui->legitymacja->setHidden(true);
    QString zpytanie="SELECT ocena,info_dod,data_wprowadzenia FROM oceny "\
                     "WHERE ID_asoc_stud_grupa = "\
                     "(SELECT ID_asoc_stud_grupa FROM asoc_stud_grupa "\
                     "WHERE indeks = ";
    zpytanie+=indekss;
    zpytanie+=")";

    QSqlQuery query(zpytanie);
         while (query.next()) {
               ocena = query.value(0).toString();//toString();
              info= query.value(1).toString();
              data= query.value(2).toString();
       }

     ui->legitymacja->setHidden(false);
    ui->legitymacja->setText((karta.getImie()+" "+karta.getNazwisko()+"\nOcena:").c_str()+ocena+"\nInfo :"+info+"\nData :"+data);

    QTimer::singleShot(100,this,SLOT(readCard()));
}
    else
    {
        ui->legitymacja->setVisible(false);
        QTimer::singleShot(100,this,SLOT(readCard()));
    }
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
    ui->ogloszenia->setStyleSheet("background-image: url(ogl.png)");
    ui->wyniki->setStyleSheet("background-image: url(blue_dwn.png)");
    ui->konsultacje->setStyleSheet("background-image: url(blue.png)");
    //ui->legitymacja->setVisible(true);
    //ui->wyniki->enabledChange(false);
    //ui->konsultacje->setDisabled(true);


    //ui->pokoj->setPalette(wh);
    //ui->textEdit->setDisabled(false);

    ui->ogloszenia->setFontItalic(true);
    ui->konsultacje->setReadOnly(true);
    ui->wyniki->setReadOnly(true);


}

void Widget::ustaw_pokoj(int nr)
{

    ui->pokoj->setDigitCount(3);
    ui->pokoj->setDecMode();
    ui->pokoj->font();
    ui->pokoj->display(nr);
  }
struct consultacje
{
    int id;
    QString imie,nazwisko,stopien,status,email,tel ;
    QString dzien[5],od_[5],do_[5];
    int iloscKonsultacji;
    QString dniKonsultacji;

    QString wypiszKonsultacje()
    {
        dniKonsultacji="";
        for( int i=0;i<iloscKonsultacji;i++)
        {

            dniKonsultacji+=dzien[i] + " od "+ od_[i] + " do "+ do_[i]+"\n";
        }
        return dniKonsultacji;
    }
};

void Widget::konsultacje()
{
    QString num;
    static int i=0;
    static int nr=-1;
    static consultacje prowadzacy[10];

    if(nr<0 || nr>=i )
    {
        i=0;
        nr=0;


        QSqlQuery query2("SELECT id_osoby,imie,nazwisko,stopien_naukowy,status,email,nr_telefonu FROM prowadzacy ");
        while (query2.next()) {
              prowadzacy[i].id = query2.value(0).toInt();//toString();
              prowadzacy[i].imie= query2.value(1).toString();//toString();
              prowadzacy[i].nazwisko= query2.value(2).toString();
              prowadzacy[i].stopien = query2.value(3).toString();
              prowadzacy[i].status = query2.value(4).toString();//toString();
              prowadzacy[i].email = query2.value(5).toString();
              prowadzacy[i].tel = query2.value(6).toString();
               num.setNum(prowadzacy[i].id);

            QSqlQuery query("SELECT dzien,od_,do_ FROM konsultacje WHERE id_osoby = "+ num);
             int j=0;
             while(query.next()){

                prowadzacy[i].dzien[j] = query.value(0).toString();//toString();
                prowadzacy[i].od_[j] = query.value(1).toString();
                prowadzacy[i].do_[j++] = query.value(2).toString();

             }

              prowadzacy[i].iloscKonsultacji=j;
              i++;

         }
    }
    else {

    ui->konsultacje->setText("\n\nProwadzacy : "+prowadzacy[nr].stopien+" "+prowadzacy[nr].imie + " "+prowadzacy[nr].nazwisko+"\nemail : "+prowadzacy[nr].email +
                             "\n tel. "+prowadzacy[nr].tel+"\n"+prowadzacy[nr].wypiszKonsultacje());
    QString numer;
    numer.setNum(nr);

//ui->wyniki->setText("\n\nNr aktuallnego prowadzacego "+numer);
     ++nr;
 }

int czasy_wyswietlania= prowadzacy[nr].wypiszKonsultacje().length()*100;
QTimer::singleShot(czasy_wyswietlania,this,SLOT(konsultacje()));



}

 

