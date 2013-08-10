Web Interface
=============

**Geheimnis** is a project consisting of several subprojects, that shall eventually made up an embedded system.
The system, including software and hardware, shall be able to connect to a PC via RJ45 or USB port, and is accessible to PC like a website providing HTTPS accessibility.
Either a user using his browser, or a software on PC using API to submit requests to this tiny website. Requests include, importing new _Contacts/Keys/Signatures_, requesting to encrypt/decrypt texts, etc. The software on this tiny hardware displays confirmation of such requests, then proceed and send out responds. Therefore, we say this project aimed at developing a device, that helps computer users authenticating, encrypting their communications. This device may also be a part of some kind of further systems, and helps automatically, e.g. for a system that controlling the entrance.

Introduction to this Subproject
-------------------------------

This subproject aims at developing a PHP-based web interface, that runs on the embedded system, and works both on the system's screen, also on the PC's.

Using a web interface have its advantages of avoiding the complex work of developing a GTK/QT/Tkinter/... based UI in the embedded system. A modern browser
is OK. On the embedded system the web UI will not use so many JavaScript and modern techniques, if the environment is not allowed. On PC, however, we can make it
more fancy.

The web UI also implements necessary logics, that is needed in cooperating with core commands and database. For example, a auditing part is needed, to
require and confirm user's intent.

Additional Requirements
-----------------------
* _msgpack-php_ You may have to compile out a _msgpack.so_ by yourself, and remember to modify _php.ini_.
* _mcrypt-php_
