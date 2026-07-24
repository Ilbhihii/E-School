from zipfile import ZipFile, ZIP_DEFLATED
from pathlib import Path
from create_schoolbridge_presentation import shape, slide, xml_decl

OUT = Path(r"F:\backend\Presentation_SmartSchool_Interfaces_Reelles.pptx")
ROOT = Path(r"F:\backend")

def picture(i, name, x, y, w, h, rid):
    return f'''<p:pic><p:nvPicPr><p:cNvPr id="{i}" name="{name}"/><p:cNvPicPr/><p:nvPr/></p:nvPicPr><p:blipFill><a:blip r:embed="{rid}"/><a:stretch><a:fillRect/></a:stretch></p:blipFill><p:spPr><a:xfrm><a:off x="{int(x*914400)}" y="{int(y*914400)}"/><a:ext cx="{int(w*914400)}" cy="{int(h*914400)}"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom><a:ln><a:noFill/></a:ln></p:spPr></p:pic>'''

real_slides = [
    slide('Smart School Academy', 'Présentation actualisée à partir des interfaces réellement présentes dans le projet', [
        shape(10, 'hero', .9, 1.8, 11.55, 4.7, 'FFFFFF', 'E3E8F2', True),
        shape(11, 'tag', 1.35, 2.3, 3.2, .42, 'EEF2FF', None, True, 'INTERFACES RÉELLES DU PROJET', 10, True, '4F46E5', 'c'),
        shape(12, 'headline', 1.35, 3.05, 4.75, 1.35, None, None, False, 'Une plateforme éducative\nmoderne et structurée.', 26, True, '172033'),
        shape(13, 'caption', 1.35, 4.75, 4.75, .65, None, None, False, 'Les écrans suivants sont des captures des pages locales de votre application.', 14, False, '667085'),
        picture(14, 'Interface inscription', 6.65, 2.15, 5.15, 3.23, 'rId3'),
        shape(15, 'badge', 6.65, 5.65, 5.15, .45, '172033', None, True, 'Smart School Academy — interface d’inscription', 10, True, 'FFFFFF', 'c')
    ]),
    slide('Interface réelle : connexion', 'Page /login — accès sécurisé à la plateforme', [
        shape(10, 'frame', .65, 1.55, 12.05, 5.63, 'FFFFFF', 'D9E2F2', True),
        picture(11, 'Capture page connexion', .82, 1.72, 11.72, 5.28, 'rId2'),
        shape(12, 'label', 1.02, 6.52, 3.05, .32, '172033', None, True, 'CAPTURE RÉELLE — /login', 9, True, 'FFFFFF', 'c')
    ]),
    slide('Interface réelle : création de compte', 'Page /register — inscription et personnalisation du parcours', [
        shape(10, 'frame', .65, 1.55, 12.05, 5.63, 'FFFFFF', 'D9E2F2', True),
        picture(11, 'Capture page inscription', .82, 1.72, 11.72, 5.28, 'rId3'),
        shape(12, 'label', 1.02, 6.52, 3.25, .32, '172033', None, True, 'CAPTURE RÉELLE — /register', 9, True, 'FFFFFF', 'c')
    ]),
    slide('Parcours d’accès : interfaces réelles', 'Deux points d’entrée conçus avec une identité visuelle cohérente.', [
        shape(10, 'login panel', .75, 1.7, 5.85, 4.85, 'FFFFFF', 'D9E2F2', True),
        picture(11, 'Connexion réelle', .92, 1.88, 5.51, 3.98, 'rId2'),
        shape(12, 'login text', .92, 6.02, 5.51, .3, None, None, False, 'Connexion : accès à l’espace personnel.', 11, True, '344054', 'c'),
        shape(13, 'register panel', 6.75, 1.7, 5.85, 4.85, 'FFFFFF', 'D9E2F2', True),
        picture(14, 'Inscription réelle', 6.92, 1.88, 5.51, 3.98, 'rId3'),
        shape(15, 'register text', 6.92, 6.02, 5.51, .3, None, None, False, 'Inscription : création d’un parcours personnalisé.', 11, True, '344054', 'c')
    ]),
    slide('Fonctionnalités couvertes par l’application', 'Les interfaces authentifiées prolongent cette expérience avec des espaces adaptés aux rôles.', [
        shape(10, 'card 1', 1.0, 2.0, 3.25, 3.45, 'EEF2FF', None, True, 'ÉLÈVES\n\nCours et ressources\nTests et devoirs\nLives et messagerie\nSuivi du parcours', 15, True, '3730A3', 'c'),
        shape(11, 'card 2', 5.05, 2.0, 3.25, 3.45, 'E6FFFA', None, True, 'ENSEIGNANTS\n\nGestion des cours\nDevoirs et évaluations\nClasses et planning\nCommunication', 15, True, '067647', 'c'),
        shape(12, 'card 3', 9.1, 2.0, 3.25, 3.45, 'FFF4E5', None, True, 'ADMINISTRATION\n\nUtilisateurs et classes\nNiveaux et matières\nPlanning et rapports\nPilotage global', 15, True, 'B54708', 'c'),
        shape(13, 'note', 1.65, 6.05, 10.05, .48, 'FFFFFF', 'E3E8F2', True, 'Les écrans sécurisés nécessitent une session utilisateur ; la présentation utilise donc les pages publiques réellement accessibles.', 10, False, '475467', 'c')
    ])
]

types = xml_decl('''<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Default Extension="png" ContentType="image/png"/><Override PartName="/ppt/presentation.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml"/><Override PartName="/ppt/slideMasters/slideMaster1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml"/><Override PartName="/ppt/slideLayouts/slideLayout1.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml"/>''' + ''.join(f'<Override PartName="/ppt/slides/slide{i}.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slide+xml"/>' for i in range(1, len(real_slides)+1)) + '''<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>''')
pres = xml_decl('<p:presentation xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"><p:sldMasterIdLst><p:sldMasterId id="2147483648" r:id="rId1"/></p:sldMasterIdLst><p:sldIdLst>' + ''.join(f'<p:sldId id="{256+i}" r:id="rId{i+1}"/>' for i in range(1,len(real_slides)+1)) + '</p:sldIdLst><p:sldSz cx="12192000" cy="6858000" type="screen16x9"/><p:notesSz cx="6858000" cy="9144000"/></p:presentation>')
master = xml_decl('<p:sldMaster xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main"><p:cSld name="Smart School Academy"><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr/></p:spTree></p:cSld><p:clrMap bg1="lt1" tx1="dk1" bg2="lt2" tx2="dk2" accent1="accent1" accent2="accent2" accent3="accent3" accent4="accent4" accent5="accent5" accent6="accent6" hlink="hlink" folHlink="folHlink"/><p:sldLayoutIdLst><p:sldLayoutId id="1" r:id="rId1"/></p:sldLayoutIdLst><p:txStyles><p:titleStyle/><p:bodyStyle/><p:otherStyle/></p:txStyles></p:sldMaster>')
layout = xml_decl('<p:sldLayout xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" type="blank" preserve="1"><p:cSld name="Blank"><p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr/></p:spTree></p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sldLayout>')

with ZipFile(OUT, 'w', ZIP_DEFLATED) as z:
    z.writestr('[Content_Types].xml', types)
    z.writestr('_rels/.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="ppt/presentation.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>'))
    z.writestr('docProps/core.xml', xml_decl('<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/"><dc:title>Smart School Academy — Interfaces réelles</dc:title><dc:creator>Codex</dc:creator></cp:coreProperties>'))
    z.writestr('docProps/app.xml', xml_decl(f'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>Smart School Academy</Application><Slides>{len(real_slides)}</Slides></Properties>'))
    z.writestr('ppt/presentation.xml', pres)
    z.writestr('ppt/_rels/presentation.xml.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="slideMasters/slideMaster1.xml"/>' + ''.join(f'<Relationship Id="rId{i+1}" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide" Target="slides/slide{i}.xml"/>' for i in range(1,len(real_slides)+1)) + '</Relationships>'))
    z.writestr('ppt/slideMasters/slideMaster1.xml', master)
    z.writestr('ppt/slideMasters/_rels/slideMaster1.xml.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/></Relationships>'))
    z.writestr('ppt/slideLayouts/slideLayout1.xml', layout)
    z.writestr('ppt/slideLayouts/_rels/slideLayout1.xml.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster" Target="../slideMasters/slideMaster1.xml"/></Relationships>'))
    z.writestr('ppt/media/login.png', (ROOT / 'public' / 'presentation-login.png').read_bytes())
    z.writestr('ppt/media/register.png', (ROOT / 'public' / 'presentation-register.png').read_bytes())
    for i, item in enumerate(real_slides, 1):
        z.writestr(f'ppt/slides/slide{i}.xml', item)
        z.writestr(f'ppt/slides/_rels/slide{i}.xml.rels', xml_decl('<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout" Target="../slideLayouts/slideLayout1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/login.png"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/register.png"/></Relationships>'))

print(OUT)
