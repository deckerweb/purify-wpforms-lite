# Fragen nach Themen

[English](FAQ.en.md)

## Einstieg und Alltag

### Schaltet Purify Pro-Funktionen frei?

Nein. Premium-Funktionen bleiben nicht verfügbar. Purify entfernt nur bekannte Werbeelemente.

### Welche Versionen werden unterstützt?

Die Bereinigung arbeitet mit WPForms Lite 1.9.4.2 und 2.0.2.2. Bei unbekannten Versionen, fehlendem WPForms und WPForms Pro pausiert sie. Die Pluginliste erklärt den Status.

### Wo sind die Einstellungen?

Die Bereinigung erfolgt automatisch. Purify fügt keine eigene Einstellungsseite hinzu. WPForms-Einstellungen und Lite-Connect-Sicherungen bleiben erreichbar. Der Fortschritt der Checkliste bleibt erhalten; ihr Menüpunkt und automatische Einrichtungsweiterleitungen werden ausgeblendet. Eintrags-Import/-Export, Eintrags-Automatisierung und die KI/MCP-Werbeansichten entfallen; vorhandene Einstellungen bleiben unverändert.

### Ändert Purify Formulare oder Hinweise?

Formulardaten und WPForms-Einstellungen werden nicht verändert. Benachrichtigungen, Fehler-/Sicherheitshinweise und tatsächliche Zahlungsgebühren bleiben sichtbar. WPConsent-Installationen über WPForms werden blockiert; die native manuelle Installation bleibt möglich. Purify entfernt keine installierten Plugins und aktiviert oder deaktiviert weder Lite Connect noch vorhandene KI-Zugriffseinstellungen.

### Kann ich ein Snippet verwenden?

Ja. Importiere die Code-Snippets-JSON in Code Snippets oder Advanced Scripts beziehungsweise die separate WPCode-JSON in WPCode Lite/Pro. Verwende nur eine Fassung gleichzeitig. Snippets enthalten die Bereinigung ohne gemeinsamen Katalog, Updater und Plugin-Änderungsverlauf.

## Technische Fragen

### Funktioniert Multisite und was passiert bei der Deinstallation?

Die Bereinigung gilt für die aktuelle Website. Netzwerkaktivierung wird unterstützt. Purify speichert keine Formulardaten oder Bereinigungseinstellungen. Deaktivierung erhält Daten; die Deinstallation bereinigt temporäre Library-Daten erst nach Entfernung ihres letzten Hosts. Gemeinsame Einstellungen bleiben erhalten, sofern die separate Library-Löschoption nicht aktiviert wurde. WPForms-Daten werden niemals gelöscht.

### Welche externen Verbindungen gibt es?

Der Plugin-Updater prüft GitHub über WordPress-Updateabfragen. Der optionale Onlineabruf der Library ist zunächst ausgeschaltet und kontaktiert erst nach Aktivierung den freigegebenen GitHub-Katalog. Manuell angestoßene Kataloginstallationen laden freigegebene Pakete. Purify ergänzt keine Telemetrie. Snippets ergänzen keine externen Abrufe.
