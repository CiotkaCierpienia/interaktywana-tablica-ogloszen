#-------------------------------------------------
#
# Project created by QtCreator 2011-03-17T22:22:26
#
#-------------------------------------------------

QT += core gui
QT += SQL

TARGET = display
TEMPLATE = app


SOURCES += main.cpp\
        widget.cpp \
	CardReader.cpp

HEADERS  += widget.h \
    CardReader.h #\
 #   Mythread.h

FORMS    += widget.ui

INCLUDEPATH += /usr/include/QtSql
INCLUDEPATH += /usr/include/PCSC

LIBS += -lQtSql
LIBS += -lpcsclite
