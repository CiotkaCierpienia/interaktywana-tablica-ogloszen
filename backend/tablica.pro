#-------------------------------------------------
#
# Project created by QtCreator 2011-03-17T22:22:26
#
#-------------------------------------------------

QT       += core gui
QT += SQL

TARGET = display
TEMPLATE = app


SOURCES += main.cpp\
        widget.cpp

HEADERS  += widget.h \
    Cardreader.h \
    Cardreader.h \
    Mythread.h

FORMS    += widget.ui \
    widget.ui

INCLUDEPATH += /usr/include/QtSql
LIBS += -lQtSql
