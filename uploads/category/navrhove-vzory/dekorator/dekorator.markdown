Přidává další funkcionalitu k objektu tím, že objekt "zabalí" do jiného objektu, který má na starosti pouze tuto přidanou funkcionalitu; zbytek činnosti deleguje na "zabalený" objekt.

Tím umožňuje přidávat funkčnost dynamicky, ale nezvyšovat počet tříd kombinacemi jejich možností
- počet kombinací by jinak zvyšoval počet tříd kombinatoricky

Další funkcionality se stejně snadno přidávají bez ovlivnění stávajících tříd.

Příklad z Javy - vše, co je nabaleno např. na _java.io.Reader - BufferedReader, FileReader, String Reader, InputStreamReader_ atd...

```
BufferedReader bfr = new BufferedReader(
    new FileReader(
    new File(celeJmenoSouboru)));

```

Další vhodné použití je v případě plánovaných postupných úprav a rozšiřování:

![Obrázek](../uploads/category/navrhove-vzory/dekorator/attachments/dekorator_tutorial.png)

```
public abstract class AText_Komponenta {
    public abstract String textProVypis();
}

```

*TextNormalni\_KonkretniKomponenta* jako jediná má v sobě data, se kterými se bude pracovat - *poskytovynyText*

```
public class TextNormalni_KonkretniKomponenta extends AText_Komponenta {
    private String poskytovanyText;
    public TextNormalni_KonkretniKomponenta(String poskytovanyText) {
       this.poskytovanyText = poskytovanyText;
    }
    @Override
    public String textProVypis() {
        return poskytovanyText;
    }
}

```

"dekorování", tj. přidání funkčnosti je pomocí *super.textProVypis().toLowerCase()*

```
public class Maly_KonkretniDekorator extends AText_Dekorator {
    public Maly_KonkretniDekorator(AText_Komponenta dekorovanyText) {
        super(dekorovanyText);
    }
    @Override
    public String textProVypis() {
       return super.textProVypis().toLowerCase() + " (maly) ";
    }
}

```

```
public class Velky_KonkretniDekorator extends AText_Dekorator {
    public Velky_KonkretniDekorator(AText_Komponenta dekorovanyText) {
        super(dekorovanyText);
    }
    @Override
    public String textProVypis() {
        return super.textProVypis().toUpperCase() + " (VELKY) ";
    }
}

```

```
public class Podtrzeny_KonkretniDekorator extends AText_Dekorator {
    public Podtrzeny_KonkretniDekorator(AText_Komponenta dekorovanyText) {
        super(dekorovanyText);
    }
    @Override
    public String textProVypis() {
        StringBuilder sb = new StringBuilder("\n");
        String pomocny = super.textProVypis();
        for (int i = 0; i < pomocny.length(); i++) {
        sb.append("=");
    }
    return pomocny + sb.toString() + " (podtrzeny) ";
    }
}

```

*Aplikace* může vytvářet stejně jako
- instance nedekorovaného textu - _text_
- instance jednotlivých dekorátorů - _textP_
- instance kombinací dekorátorů - *textVP* nebo *textPoM*

```
public class Aplikace {
    public static final void main(String[] args) {
        AText_Komponenta text = new TextNormalni_KonkretniKomponenta("Poskytovany TEXT");
        System.out.println(text.textProVypis());
        AText_Komponenta textP = new Prolozeny_KonkretniDekorator(text);
        System.out.println(textP.textProVypis());
        AText_Komponenta textVP = new Velky_KonkretniDekorator(
                                  new Prolozeny_KonkretniDekorator(text));
        System.out.println(textVP.textProVypis());
        AText_Komponenta textPoM = new Podtrzeny_KonkretniDekorator(
                                   new Maly_KonkretniDekorator(text));
        System.out.println(textPoM.textProVypis());
    }
}
```