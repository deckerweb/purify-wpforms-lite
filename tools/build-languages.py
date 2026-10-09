"""Compile host and shared component translations from maintained PO resources."""
from pathlib import Path
import subprocess,json,gettext
r=Path(__file__).resolve().parents[1];s=r/'work/language-build';s.mkdir(parents=True,exist_ok=True)
files=['purify-wpforms-lite.php']+['includes/'+p.name for p in (r/'includes').glob('*.php') if not p.name.startswith('deckerweb-')]
subprocess.run(['xgettext','--language=PHP','--from-code=UTF-8','--keyword=__:1','--keyword=esc_html__:1','--keyword=esc_html_x:1,2c','--package-name=Purify WPForms Lite','-o',str(s.resolve()/'host.pot')]+files,cwd=r,check=True)
lib=r/'includes/deckerweb-plugin-library/languages';(s/'library.pot').write_text('msgid ""\nmsgstr "Content-Type: text/plain; charset=UTF-8\\n"\n\n'+(lib/'messages.pot').read_text());subprocess.run(['msgcat','--use-first',str(s/'host.pot'),str(s/'library.pot'),'-o',str(r/'languages/purify-wpforms-lite.pot')],check=True)
c=json.loads((r/'resources/content.json').read_text());pairs={a:b for en,de in zip(c['en']['history'],c['de']['history']) for (_,a),(_,b) in zip(en[2],de[2])};pairs.update({'Unreleased':'Unveröffentlicht','2025-04-05':'2025-04-05','Donate':'Spenden','Join the newsletter':'Newsletter abonnieren','Close':'Schließen','Changelog':'Änderungsverlauf','Purify WPForms Lite':'Purify WPForms Lite','Purify WPForms Lite changelog':'Purify WPForms Lite Änderungsverlauf','New':'Neu','Improved':'Verbessert','Fixed':'Behoben','Misc':'Sonstiges'})
pairs.update({
'WPConsent installation through WPForms is blocked by Purify. Manual installation through WordPress remains available.':'Purify blockiert die WPConsent-Installation über WPForms. Die manuelle Installation über WordPress bleibt möglich.',
'%s platform fees apply in addition to payment provider fees.':'%s Plattformgebühren fallen zusätzlich zu den Gebühren des Zahlungsanbieters an.'})
for locale in ['de_DE','de_DE_formal']:
 ref=(r/'languages'/('purify-wpforms-lite-'+locale+'.po')).read_text().replace('purify-wpcode-lite','purify-wpforms-lite').replace('Purify WPCode Lite','Purify WPForms Lite').replace('WPCode Lite','WPForms Lite').replace('WPCode Pro','WPForms Pro')
 (s/(locale+'-reference.po')).write_text(ref)
 header='Project-Id-Version: Purify WPForms Lite 1.1.0\nPO-Revision-Date: 2026-10-10 00:00+0200\nLast-Translator: David Decker – DECKERWEB\nLanguage-Team: German\nLanguage: '+locale+'\nContent-Type: text/plain; charset=UTF-8\nMIME-Version: 1.0\nContent-Transfer-Encoding: 8bit\nPlural-Forms: nplurals=2; plural=(n != 1);\n'
 (s/(locale+'.po')).write_text('msgid ""\nmsgstr '+json.dumps(header,ensure_ascii=False)+'\n\n'+'\n\n'.join('msgid '+json.dumps(a,ensure_ascii=False)+'\nmsgstr '+json.dumps(b,ensure_ascii=False) for a,b in pairs.items())+'\n')
 subprocess.run(['msgcat','--use-first',str(s/(locale+'.po')),str(s/(locale+'-reference.po')),str(lib/(locale+'.po')),'-o',str(s/(locale+'-merged.po'))],check=True)
 dest=r/'languages'/('purify-wpforms-lite-'+locale+'.po');subprocess.run(['msgmerge','--no-fuzzy-matching',str(s/(locale+'-merged.po')),str(r/'languages/purify-wpforms-lite.pot'),'-o',str(dest)],check=True)
 subprocess.run(['msgattrib','--no-obsolete',str(dest),'-o',str(s/(locale+'-clean.po'))],check=True);dest.write_bytes((s/(locale+'-clean.po')).read_bytes());subprocess.run(['msgfmt','--check',str(dest),'-o',str(dest.with_suffix('.mo'))],check=True);dest.with_suffix('.l10n.php').unlink(missing_ok=True)
 subprocess.run(['msgattrib','--untranslated','--no-obsolete',str(dest)],stdout=open(s/(locale+'-untranslated.txt'),'w'),check=True)
 print(locale,'messages',len(gettext.GNUTranslations(dest.with_suffix('.mo').open('rb'))._catalog))
