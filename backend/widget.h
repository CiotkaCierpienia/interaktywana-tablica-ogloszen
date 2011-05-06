#ifndef WIDGET_H
#define WIDGET_H

#include <QWidget>
#include <qsqldatabase.h>
#include <qsqlrecord.h>
#include <qsqlquery.h>
#include <qsqlerror.h>
#include <QMessageBox>
#include "CardReader.h"
#include <vector>
#include <algorithm>
#include <list>
#include <QThread>


namespace Ui {
    class Widget;
}

class Widget : public QWidget
{
    Q_OBJECT

public:
    explicit Widget(QWidget *parent = 0);
    ~Widget();
    void setup();
    void polacz(QSqlDatabase baza);
    void konsultacje();
    void pobierz();
    void rozlacz(QSqlDatabase baza);

    void ustaw_pokoj(int nr);
    void wyniki();
    void test();
    std::list<int> sheduler(int priorytety[], int n);
private:
    Ui::Widget *ui;
    CardReader karta;


public slots:

    void ogloszenia();
    void readCard();
    //void on_tableView_activated(QModelIndex index);
};

#endif // WIDGET_H
