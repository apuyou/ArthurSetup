ArthurSetup
===========

A tool to generate config files based on user input

This tool was originally created when we wanted to migrate [payutc](https://github.com/payutc) to the Propel ORM. It allows config files containing the same parameters repeatedly to be generated automatically.

It first scans the files listed in the JSON configuration, then asks the user for input and finally creates the config files based on this.
