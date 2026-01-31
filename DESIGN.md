# 🎨 DESIGN-GUIDE - MyPlanio

**Entwickelt von:** Justin Jung | **Kontakt:** justinjung@t-online.de

---

## 📜 Lizenz

**Private Nutzung:** Kostenlos mit Namensnennung  
**Geschäftliche Nutzung:** Lizenz erforderlich - justinjung@t-online.de

---

## Farbschema

### Hauptfarben (Dunkelgrau-Design)

```css
Primärer Hintergrund:     #1a1a1a  (sehr dunkel)
Sekundärer Hintergrund:   #2d2d2d  (dunkelgrau)
Tertiärer Hintergrund:    #3a3a3a  (mittel-dunkelgrau)
Hover-Hintergrund:        #404040  (aufgehelltes grau)

Primärer Text:            #e8e8e8  (fast weiß)
Sekundärer Text:          #b0b0b0  (hellgrau)
Gedämpfter Text:          #808080  (mittelgrau)

Border/Rahmen:            #4a4a4a  
Border Hell:              #555555
```

### Pastellfarben für Termine & Gruppen

Die App verwendet 8 sanfte Pastellfarben für bessere Übersicht:

```css
🌸 Rosa:      #ffb3ba  (Standard-Farbe)
🍑 Orange:    #ffdfba
💛 Gelb:      #ffffba
🌿 Grün:      #baffc9
💙 Blau:      #bae1ff
💜 Lila:      #e0bbff
🌊 Mint:      #c9fff4
🍊 Pfirsich:  #ffd4ba
```

**Verwendung:**
- Event-Termin Hintergrundfarbe
- Gruppen-Markierung (linker Border)
- Farbauswahl in Formularen

### Akzentfarben

```css
Erfolg (Success): #baffc9  (Pastellgrün)
Fehler (Error):   #ffb3ba  (Pastellrosa)
Info:             #bae1ff  (Pastellblau)
```

---

## UI-Komponenten

### Navigation
- **Hintergrund:** Dunkelgrau (#2d2d2d)
- **Text:** Hellgrau (#b0b0b0)
- **Hover:** Weiß (#e8e8e8)
- **Border-Bottom:** 1px solid #4a4a4a

### Buttons

**Standard:**
- Hintergrund: #3a3a3a
- Border: 2px solid #555555
- Text: #e8e8e8
- Hover: #404040

**Primary:**
- Hintergrund: #bae1ff (Pastellblau)
- Text: #1a1a1a (dunkel, für Kontrast)
- Hover: #9dd1ff (aufgehellt)

**Danger:**
- Hintergrund: #ffb3ba (Pastellrosa)
- Text: #1a1a1a
- Hover: #ff9ba3

### Formulare

**Input-Felder:**
- Hintergrund: #2d2d2d
- Border: 2px solid #4a4a4a
- Text: #e8e8e8
- Focus: Border wird zu #bae1ff

**Labels:**
- Farbe: #b0b0b0
- Font-Weight: 600

### Kalender

**Day-Header:**
- Hintergrund: #3a3a3a
- Text: #e8e8e8
- Border: 1px solid #4a4a4a

**Kalender-Tag:**
- Hintergrund: #2d2d2d
- Hover: #3a3a3a
- Border: 1px solid #4a4a4a

**Heute-Markierung:**
- Hintergrund: #3a3a3a
- Border: 2px solid #bae1ff (Pastellblau)
- Tag-Nummer: Farbe #bae1ff

**Event-Items:**
- Hintergrund: Pastellfarbe des Termins
- Text: #1a1a1a (dunkel für Lesbarkeit)
- Border-Left: 3px solid in gleicher Farbe
- Font-Weight: 600

---

## Anpassungen vornehmen

### Farben ändern

Bearbeiten Sie `/css/style.css` und ändern Sie die CSS-Variablen:

```css
:root {
    --bg-primary: #1a1a1a;
    --bg-secondary: #2d2d2d;
    --text-primary: #e8e8e8;
    
    /* Pastellfarben anpassen */
    --pastel-pink: #ffb3ba;
    --pastel-blue: #bae1ff;
}
```

Viel Spaß beim Anpassen! 🎨
