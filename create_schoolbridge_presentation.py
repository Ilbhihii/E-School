from zipfile import ZipFile, ZIP_DEFLATED
from xml.sax.saxutils import escape
from pathlib import Path

OUT = Path(r"F:\backend\Presentation_SchoolBridge.pptx")
EMU = 914400
W, H = 13.333 * EMU, 7.5 * EMU

def xml_decl(body):
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' + body

def color(fill):
    return f'<a:solidFill><a:srgbClr val="{fill}"/></a:solidFill>' if fill else '<a:noFill/>'

def shape(i, name, x, y, w, h, fill=None, line=None, radius=False, text='', size=18, bold=False, fg='172033', align='l'):
    prst = 'roundRect' if radius else 'rect'
    line_xml = '<a:ln><a:noFill/></a:ln>' if not line else f'<a:ln w="12700"><a:solidFill><a:srgbClr val="{line}"/></a:solidFill></a:ln>'
    paras = []
    for n, paragraph in enumerate(text.split('\n')):
        rpr = f'<a:rPr lang="fr-FR" sz="{int(size*100)}" b="{1 if bold else 0}">{color(fg)}</a:rPr>'
        paras.append(f'<a:p><a:pPr algn="{align}"/><a:r>{rpr}<a:t>{escape(paragraph)}</a:t></a:r><a:endParaRPr lang="fr-FR" sz="{int(size*100)}"/></a:p>')
    tx = '' if not text else f'<p:txBody><a:bodyPr wrap="square" lIns="127000" rIns="127000" tIns="63500" bIns="63500"/><a:lstStyle/>{"".join(paras)}</p:txBody>'
    return f'''<p:sp><p:nvSpPr><p:cNvPr id="{i}" name="{escape(name)}"/><p:cNvSpPr/><p:nvPr/></p:nvSpPr><p:spPr><a:xfrm><a:off x="{int(x*EMU)}" y="{int(y*EMU)}"/><a:ext cx="{int(w*EMU)}" cy="{int(h*EMU)}"/></a:xfrm><a:prstGeom prst="{prst}"><a:avLst/></a:prstGeom>{color(fill)}{line_xml}</p:spPr>{tx}</p:sp>'''

def slide(title, subtitle='', elements=[]):
    base = [shape(1, 'Background', 0, 0, 13.333, 7.5, 'F7F9FC'),
            shape(2, 'Accent', 0, 0, .18, 7.5, '4F46E5'),
            shape(3, 'Title', .7, .45, 11.9, .55, None, None, False, title, 27, True, '172033'),
            shape(4, 'Line', .7, 1.14, 11.8, .03, 'D9E2F2')]
    if subtitle:
        base.append(shape(5, 'Subtitle', .72, 1.25, 11.3, .35, None, None, False, subtitle, 12, False, '667085'))
    base.extend(elements)
    tree = '<p:nvGrpSpPr><p:cNvPr id="0" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr/>' + ''.join(base)
    return xml_decl(f'<p:sld xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"><p:cSld><p:spTree>{tree}</p:spTree></p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sld>')

def card(i, x, y, w, h, title, content, accent='4F46E5'):
    return [shape(i, f'card {title}', x, y, w, h, 'FFFFFF', 'E3E8F2', True),
            shape(i+1, 'accent', x, y, .09, h, accent, None, True),
            shape(i+2, 'card title', x+.28, y+.18, w-.5, .34, None, None, False, title, 16, True, '172033'),
            shape(i+3, 'card content', x+.28, y+.62, w-.5, h-.75, None, None, False, content, 11.5, False, '667085')]

slides = []
slides.append(slide('SchoolBridge', 'Plateforme numérique de gestion et d’apprentissage scolaire', [
    shape(10, 'hero', .75, 1.85, 11.7, 4.6, 'FFFFFF', 'E3E8F2', True),
    shape(11, 'tag', 1.2, 2.3, 2.25, .42, 'EEF2FF', None, True, 'ÉDUCATION CONNECTÉE', 10, True, '4F46E5', 'c'),
    shape(12, 'headline', 1.2, 2.9, 5.25, 1.2, None, None, False, 'Un espace unique\npour apprendre, enseigner et piloter.', 25, True, '172033'),
    shape(13, 'description', 1.2, 4.35, 4.9, .75, None, None, False, 'Une expérience claire pour les élèves, les enseignants et l’administration.', 14, False, '667085'),
    shape(14, 'cta', 1.2, 5.45, 1.75, .48, '4F46E5', None, True, 'PROJET LARAVEL', 10, True, 'FFFFFF', 'c'),
    shape(15, 'screen', 7.0, 2.25, 4.55, 3.55, '172033', None, True),
    shape(16, 'screen top', 7.2, 2.5, 4.15, .35, '27324A', None, True, '  SchoolBridge     Tableau de bord', 10, True, 'FFFFFF'),
    shape(17, 'menu', 7.2, 3.05, .8, 2.45, '222C42', None, True, '⌂\n\n▣\n\n✉', 14, False, 'C7D2FE', 'c'),
    shape(18, 'main', 8.2, 3.05, 3.15, 2.45, 'FFFFFF', None, True),
    shape(19, 'welcome', 8.45, 3.3, 2.65, .42, None, None, False, 'Bonjour, Amine 👋', 15, True, '172033'),
    shape(20, 'mini card 1', 8.45, 4.0, 1.15, .92, 'EEF2FF', None, True, '4\nCours', 11, True, '4F46E5', 'c'),
    shape(21, 'mini card 2', 9.78, 4.0, 1.15, .92, 'ECFDF3', None, True, '87%\nProgression', 10, True, '067647', 'c')
]))

slides.append(slide('Le besoin auquel répond SchoolBridge', 'Centraliser les parcours pédagogiques et les échanges.', sum([
    card(10, .8, 1.9, 3.65, 3.8, 'Pour les élèves', 'Accéder aux cours, devoirs, tests et séances en direct.\n\nSuivre sa progression et recevoir les informations utiles.', '4F46E5'),
    card(20, 4.85, 1.9, 3.65, 3.8, 'Pour les enseignants', 'Organiser les contenus, partager des ressources et suivre les résultats.\n\nCommuniquer directement avec les apprenants.', '0E9384'),
    card(30, 8.9, 1.9, 3.65, 3.8, 'Pour l’administration', 'Gérer utilisateurs, classes, matières et indicateurs.\n\nAssurer une vision globale de l’établissement.', 'F79009')
], [])))

slides.append(slide('Des parcours adaptés à chaque rôle', 'Une navigation simple avec des droits et outils dédiés.', [
    shape(10,'flow line', 1.8,3.63,9.6,.06,'CBD5E1'),
    shape(11,'role 1',1.1,2.25,2.6,2.6,'FFFFFF','E3E8F2',True), shape(12,'role icon 1',1.85,2.6,1.1,.78,'EEF2FF',None,True,'ÉLÈVE',17,True,'4F46E5','c'), shape(13,'role text 1',1.4,3.7,2.0,.65,None,None,False,'Apprendre\n& progresser',13,True,'172033','c'),
    shape(14,'role 2',5.35,2.25,2.6,2.6,'FFFFFF','E3E8F2',True), shape(15,'role icon 2',6.1,2.6,1.1,.78,'E6FFFA',None,True,'PROF',17,True,'0E9384','c'), shape(16,'role text 2',5.65,3.7,2.0,.65,None,None,False,'Enseigner\n& accompagner',13,True,'172033','c'),
    shape(17,'role 3',9.6,2.25,2.6,2.6,'FFFFFF','E3E8F2',True), shape(18,'role icon 3',10.35,2.6,1.1,.78,'FFF4E5',None,True,'ADMIN',15,True,'F79009','c'), shape(19,'role text 3',9.9,3.7,2.0,.65,None,None,False,'Organiser\n& piloter',13,True,'172033','c'),
    shape(20,'footer',2.25,5.55,8.8,.52,'EEF2FF',None,True,'Des données partagées, une expérience maîtrisée pour chaque utilisateur.',13,False,'344054','c')
]))

slides.append(slide('Interface élève : tableau de bord', 'Une vue d’ensemble immédiate des activités et de la progression.', [
    shape(10,'browser',.9,1.75,11.6,4.95,'FFFFFF','D9E2F2',True), shape(11,'topbar',.9,1.75,11.6,.48,'172033',None,True,'  SchoolBridge      Accueil    Matières    Mes cours    Agenda',11,True,'FFFFFF'),
    shape(12,'sidebar',.9,2.23,2.0,4.47,'F1F5F9',None,False,'  Tableau de bord\n\n  Mes cours\n\n  Devoirs\n\n  Tests\n\n  Messages',12,False,'475467'),
    shape(13,'greeting',3.3,2.55,5.0,.46,None,None,False,'Bonjour, Amine 👋',20,True,'172033'),
    shape(14,'subgreeting',3.3,3.03,4.5,.3,None,None,False,'Voici votre activité de la semaine.',11,False,'667085'),
    shape(15,'stat1',3.3,3.55,1.85,1.05,'EEF2FF',None,True,'04\nCours actifs',14,True,'4F46E5','c'),
    shape(16,'stat2',5.35,3.55,1.85,1.05,'ECFDF3',None,True,'87%\nProgression',14,True,'067647','c'),
    shape(17,'stat3',7.4,3.55,1.85,1.05,'FFF4E5',None,True,'02\nDevoirs à faire',14,True,'B54708','c'),
    shape(18,'agenda',3.3,5.0,5.95,1.15,'FFFFFF','E3E8F2',True,'Prochain cours   •   Mathématiques — Lundi, 10:00\nLien de séance et documents disponibles',12,False,'344054'),
    shape(19,'chart',9.65,2.65,2.2,3.5,'FFFFFF','E3E8F2',True,'Progression\n\n  90%\n\n  75%\n\n  62%',12,True,'4F46E5','c')
]))

slides.append(slide('Interface élève : espace cours', 'Le contenu pédagogique est organisé par matière et par module.', [
    shape(10,'app',.9,1.75,11.6,4.95,'FFFFFF','D9E2F2',True), shape(11,'left nav',.9,1.75,2.1,4.95,'172033',None,True,'  SchoolBridge\n\n  ‹ Mes cours\n\n  Mathématiques\n\n  Physique\n\n  Français',12,False,'FFFFFF'),
    shape(12,'breadcrumb',3.45,2.15,6.5,.3,None,None,False,'Mes cours  /  Mathématiques  /  Algèbre',11,False,'667085'),
    shape(13,'lesson title',3.45,2.65,5.6,.55,None,None,False,'Équations du second degré',21,True,'172033'),
    shape(14,'video',3.45,3.45,4.7,2.15,'1E293B',None,True,'▶\nVidéo du cours',20,True,'FFFFFF','c'),
    shape(15,'course list',8.55,3.0,3.05,2.75,'F8FAFC','E3E8F2',True,'CONTENU DU MODULE\n\n✓ Introduction\n✓ Forme canonique\n• Exercices guidés\n○ Quiz de validation',11,False,'344054'),
    shape(16,'download',3.45,5.95,2.4,.4,'EEF2FF',None,True,'↓ Télécharger le support',10,True,'4F46E5','c')
]))

slides.append(slide('Interface enseignant : piloter sa classe', 'Des outils directs pour publier, évaluer et échanger.', [
    shape(10,'app',.9,1.75,11.6,4.95,'FFFFFF','D9E2F2',True), shape(11,'top',.9,1.75,11.6,.52,'0F766E',None,True,'  SchoolBridge Enseignant                         🔔  Profil',11,True,'FFFFFF'),
    shape(12,'side',.9,2.27,2.2,4.43,'F0FDFA',None,False,'  Accueil\n\n  Mes classes\n\n  Cours\n\n  Devoirs\n\n  Résultats',12,False,'115E59'),
    shape(13,'heading',3.45,2.6,5.2,.45,None,None,False,'Classe Terminale — Mathématiques',18,True,'172033'),
    shape(14,'publish',9.5,2.53,1.9,.45,'0F766E',None,True,'+ Publier un contenu',10,True,'FFFFFF','c'),
    shape(15,'table',3.45,3.35,7.9,2.45,'FFFFFF','E3E8F2',True,'ACTIVITÉ                         ÉTAT              ACTION\n\nCours : Fonctions               Publié           Modifier\nDevoir : Exercices 4            À corriger       Ouvrir\nQuiz : Chapitre 2               28 réponses      Consulter',11,False,'344054'),
    shape(16,'notice',3.45,6.05,7.9,.38,'E6FFFA',None,True,'✓  28 élèves ont consulté le dernier contenu.',10,False,'067647')
]))

slides.append(slide('Interface administration : garder la maîtrise', 'Une vue synthétique pour organiser l’établissement.', [
    shape(10,'app',.9,1.75,11.6,4.95,'FFFFFF','D9E2F2',True), shape(11,'side',.9,1.75,2.15,4.95,'1E293B',None,True,'  ADMINISTRATION\n\n  Tableau de bord\n\n  Utilisateurs\n\n  Classes & niveaux\n\n  Matières\n\n  Rapports',11,False,'E2E8F0'),
    shape(12,'heading',3.45,2.15,5.5,.4,None,None,False,'Vue d’ensemble',20,True,'172033'),
    shape(13,'metric1',3.45,2.85,2.05,1.05,'EEF2FF',None,True,'1 248\nUtilisateurs',15,True,'4F46E5','c'),
    shape(14,'metric2',5.72,2.85,2.05,1.05,'ECFDF3',None,True,'36\nClasses',15,True,'067647','c'),
    shape(15,'metric3',7.99,2.85,2.05,1.05,'FFF4E5',None,True,'94%\nComptes actifs',15,True,'B54708','c'),
    shape(16,'metric4',10.26,2.85,1.15,1.05,'F4F3FF',None,True,'18\nCours',14,True,'6941C6','c'),
    shape(17,'users',3.45,4.35,4.5,1.75,'FFFFFF','E3E8F2',True,'Dernières inscriptions\n\nSara B.     Élève      Active\nYassine M.  Professeur Actif',11,False,'344054'),
    shape(18,'actions',8.25,4.35,3.15,1.75,'FFFFFF','E3E8F2',True,'Actions rapides\n\n+ Créer une classe\n+ Ajouter un utilisateur\n+ Exporter un rapport',11,False,'344054')
]))

slides.append(slide('Architecture fonctionnelle', 'Une base web Laravel conçue autour des usages scolaires.', [
    shape(10,'front',1.0,2.25,2.5,2.1,'EEF2FF','C7D2FE',True,'INTERFACES WEB\n\nÉlève\nEnseignant\nAdministration',15,True,'3730A3','c'),
    shape(11,'arrow1',3.75,3.1,1.05,.35,None,None,False,'→',26,True,'4F46E5','c'),
    shape(12,'core',5.05,2.25,3.0,2.1,'ECFDF3','A6F4C5',True,'APPLICATION LARAVEL\n\nAuthentification\nGestion des rôles\nLogique pédagogique',15,True,'067647','c'),
    shape(13,'arrow2',8.3,3.1,1.05,.35,None,None,False,'→',26,True,'0E9384','c'),
    shape(14,'data',9.6,2.25,2.5,2.1,'FFF4E5','FEDF89',True,'DONNÉES\n\nUtilisateurs\nCours & modules\nDevoirs & résultats',15,True,'B54708','c'),
    shape(15,'note',1.45,5.15,10.3,.6,'FFFFFF','E3E8F2',True,'Une plateforme centralisée, sécurisée et évolutive pour la vie scolaire.',14,False,'475467','c')
]))

slides.append(slide('SchoolBridge en résumé', 'Une expérience numérique utile, lisible et orientée réussite.', [
    shape(10,'summary',.95,1.85,11.55,3.45,'FFFFFF','E3E8F2',True),
    shape(11,'point1',1.45,2.3,3.0,1.85,'EEF2FF',None,True,'01\nApprendre\nAccès simple aux contenus',15,True,'4F46E5','c'),
    shape(12,'point2',5.15,2.3,3.0,1.85,'E6FFFA',None,True,'02\nAccompagner\nSuivi pédagogique précis',15,True,'067647','c'),
    shape(13,'point3',8.85,2.3,3.0,1.85,'FFF4E5',None,True,'03\nPiloter\nGestion centralisée',15,True,'B54708','c'),
    shape(14,'thanks',2.7,5.85,7.9,.52,'4F46E5',None,True,'Merci — Questions & échanges',16,True,'FFFFFF','c')
]))

content_types = xml_decl('''<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/ppt/presentation.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"/><Override PartName="/ppt/slideMasters/slideMaster1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml"/><Override PartName="/ppt/slideLayouts/slideLayout1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml"/>''' + ''.join(f'<Override PartName="/ppt/slides/slide{i}.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slide+xml"/>' for i in range(1, len(slides)+1)) + '''<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>''')
presentation = xml_decl('<p:presentation xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"><p:sldMasterIdLst><p:sldMasterId id="2147483648" r:id="rId1"/></p:sldMasterIdLst><p:sldIdLst>' + ''.join(f'<p:sldId id="{256+i}" r:id="rId{i+1}"/>' for i in range(1,len(slides)+1)) + '</p:sldIdLst><p:sldSz cx="12192000" cy="6858000" type="screen16x9"/><p:notesSz cx="6858000" cy="9144000"/></p:presentation>')
master = xml_decl('<p:sldMaster xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"><p:cSld name="SchoolBridge"><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr/></p:spTree></p:cSld><p:clrMap bg1="lt1" tx1="dk1" bg2="lt2" tx2="dk2" accent1="accent1" accent2="accent2" accent3="accent3" accent4="accent4" accent5="accent5" accent6="accent6" hlink="hlink" folHlink="folHlink"/><p:sldLayoutIdLst><p:sldLayoutId id="1" r:id="rId1"/></p:sldLayoutIdLst><p:txStyles><p:titleStyle/><p:bodyStyle/><p:otherStyle/></p:txStyles></p:sldMaster>')
layout = xml_decl('<p:sldLayout xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" type="blank" preserve="1"><p:cSld name="Blank"><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr/></p:spTree></p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sldLayout>')

with ZipFile(OUT, 'w', ZIP_DEFLATED) as z:
    z.writestr('[Content_Types].xml', content_types)
    z.writestr('_rels/.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="ppt/presentation.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>'))
    z.writestr('docProps/core.xml', xml_decl('<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:title>Présentation SchoolBridge</dc:title><dc:creator>Codex</dc:creator><dcterms:created xsi:type="dcterms:W3CDTF">2026-07-24T00:00:00Z</dcterms:created></cp:coreProperties>'))
    z.writestr('docProps/app.xml', xml_decl(f'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>SchoolBridge</Application><Slides>{len(slides)}</Slides></Properties>'))
    z.writestr('ppt/presentation.xml', presentation)
    z.writestr('ppt/_rels/presentation.xml.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="slideMasters/slideMaster1.xml"/>' + ''.join(f'<Relationship Id="rId{i+1}" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide" Target="slides/slide{i}.xml"/>' for i in range(1,len(slides)+1)) + '</Relationships>'))
    z.writestr('ppt/slideMasters/slideMaster1.xml', master)
    z.writestr('ppt/slideMasters/_rels/slideMaster1.xml.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/></Relationships>'))
    z.writestr('ppt/slideLayouts/slideLayout1.xml', layout)
    z.writestr('ppt/slideLayouts/_rels/slideLayout1.xml.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="../slideMasters/slideMaster1.xml"/></Relationships>'))
    for i, item in enumerate(slides, 1):
        z.writestr(f'ppt/slides/slide{i}.xml', item)
        z.writestr(f'ppt/slides/_rels/slide{i}.xml.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/></Relationships>'))

print(OUT)
