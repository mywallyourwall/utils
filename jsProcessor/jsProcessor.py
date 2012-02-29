#jsProcessor.py
#entnimmt //debug Zeilen und packt alle jsDateien in einem bestimmten Ordner zusammen
#Google closure compiler (compiler.jar) muss im selben Verzeichnis sein
#Ausfuehrung: python jsProcessor.py
#Obligatorischer Argument python jsProcessor.py 'deineXMLDatei.xml'
#! /usr/bin/python
import sys, fileinput, os, glob, shutil, time
from xml.etree import ElementTree

#verwenden wir einen anderen Pfad?
if len(sys.argv) > 1:
	XMLFILE =  sys.argv[1]
	print ':: with the XML file: ' + XMLFILE
else:
	print '!! No XML file - Ending'
	sys.exit(0)


DOM = ElementTree.parse(XMLFILE)
JSPATH = DOM.find('./directory').attrib['path']
JSDOC= DOM.find('./jsdoc').attrib['on']
print ':: mit dem Pfad: ' + JSPATH
PAYLOAD = JSPATH+DOM.find('./outputfile').attrib['path']
DEBUGVERSION = DOM.find('./debugversion').attrib['on']
DEBUGFILE = DOM.find('./debugversion').attrib['file']
#DELMINDIR= DOM.find('./options').attrib['deletetempdirectory']
FILESTOPACK = DOM.find('./loadorder')
JSPATHTemp = JSPATH+'temp/'


#Zeilen mit //debug werden geloescht
DEBUGSTATEMENT = '//debug'


#Google closure compiler sollte in den gleichen Ordner angelegt
pathToCompiler = ''

#payload Datei loeschen
if os.path.exists(PAYLOAD):
    ftemp = PAYLOAD
    fn=open(ftemp, 'w')
    fn.write('')
    fn.close()

#Tempverzeichnis
if not os.path.exists(JSPATHTemp):
    os.makedirs(JSPATHTemp)


#loescht alte Dateien
def cleanUp():
    shutil.rmtree(JSPATHTemp)
    print ':: Temp folder deleted'


#auf debug Zeilen testen
def testForDebug(line):
	#line = line.rstrip('\r\n')
	if not DEBUGSTATEMENT in line:
		return line
	else:
		return False

#1. bearbeitbare Dateien erzeugen
def copyJS():
    if DEBUGVERSION == 'true' and len(DEBUGFILE) > 1:
        dbfile=DEBUGFILE
        dbftemp = JSPATHTemp+dbfile[0:-3] + '.temp.js'
        dbf=open(JSPATH+dbfile, 'r')
        dbfn=open(dbftemp, 'w')
        dbfn.writelines(dbf)
        print ':: ' + dbftemp + ' created'
        dbf.close()
        dbfn.close()
    for child in FILESTOPACK:
        file=child.text
        ftemp = JSPATHTemp+file[0:-3] + '.temp.js'
        f=open(JSPATH+file, 'r')
        fn=open(ftemp, 'w')
        fn.writelines(f)
        print ':: ' + ftemp + ' created'
        f.close()
        fn.close()

#2. zusammenpacken und jsdocs erzeugen
def zusammenPacken():
	ftemp = PAYLOAD
	fn=open(ftemp, 'w')
	fn.write('')
	print PAYLOAD + ' geoeffnet'
	if DEBUGVERSION == 'true' and len(DEBUGFILE) > 1:
		dbfile=DEBUGFILE
		dbfname = dbfile[0:-3]+ '.temp.js'
		dbf=open(JSPATHTemp+dbfname, 'r')
		print '########### ' + dbfname
		dblines=dbf.readlines()
		print ':::: reading ' + dbfname
		fn.writelines(dblines)
		print ':::: ' + dbfile + ' to ' + PAYLOAD + ' added'
		dbf.close()	
	for child in FILESTOPACK:
		file=child.text
		fname = file[0:-3]+ '.temp.js'
		f=open(JSPATHTemp+fname, 'r')
		print '########### ' + fname
		lines=f.readlines()
		print ':::: reading ' + fname
		if DEBUGVERSION == 'false':
			for line in lines:
				newline=testForDebug(line)
				if newline is not False:
					fn.write(newline)
		else: 
			fn.writelines(lines)
		f.close()
		print ':::: ' + file + ' to ' + PAYLOAD + ' added'
	if JSDOC == 'true':
		os.system('java -jar jsdoc-toolkit/jsrun.jar jsdoc-toolkit/app/run.js -p -t=jsdoc-toolkit/templates/jsdoc -d=' + JSPATH + 'jsdoc ' + PAYLOAD)		
	fn.close()
	cleanUp()


#3. kompromieren
def compressPayload():
	#payload Datei loeschen
	PAYLOADMin = PAYLOAD[0:-3]+ '.min.js'
	f=open(PAYLOAD, 'r')
	fn=open(PAYLOADMin,'w')
	#del PAYLOADMin
	fn.write('')
	if not os.path.exists('compiler.jar'):
		print '!! ' + compiler.jar + ' not found - Ending'
		sys.exit(0)
	os.system('java -jar compiler.jar --js ' + PAYLOAD + ' --js_output_file ' + PAYLOADMin)
	print ':: ' + PAYLOAD + ' minified'
	f.close()
	#fn.write('/*generiert von jsProcessor.py - ' + time.strftime('%d/%m/%y %H:%M:%S', time.localtime()) + '*/')
	fn.close()

#ausfuehren
copyJS()
zusammenPacken()
compressPayload()
#if DEBUGVERSION == 'false':
	#compressPayload()

print ':: READY :: Generated by jsProcessor.py - ' + time.strftime('%d/%m/%y %H:%M:%S', time.localtime())