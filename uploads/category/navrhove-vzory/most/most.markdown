Aplikace komunikuje se třídou, která je pouze zprostředkovatelem nabízené služby
- skutečnou službu má na starosti jiná třída
- třída využívající službu ji neumí nebo nechce zajistit
 - jiným častým případem je, že službu od začátku vytváří někdo jiný - týmová práce
- typicky je to služba, která je nějak implementačně závislá a proto se může v budoucnu měnit
 - pak se snadno vymění původní třída za jinou, bez ovlivnění zbývajících tříd

nebo mají příbuzné třídy jednu (či více) služeb odlišných

![](../uploads/category/navrhove-vzory/most/attachments/most_tutorial.png)

_IUlozeniZbozi\_Implementator_ popisuje externě zajišťovanou službu

```
public interface IUlozeniZbozi_Implementator {
    public void uloz(String nazev, double cena);
}
```

_AZbozi\_Abstrakce_ je předek tříd, kderé budou službu využívat
využívaná služba (_uloz()_) se může jmenovat jinak - zde _ulozZbozi()_
ostatní služby si třída může zajišťovat vlastními prostředky - zde _zmenCenu()_

```
public abstract class AZbozi_Abstrakce {
    protected IUlozeniZbozi_Implementator ulozeniZbozi;
    public AZbozi_Abstrakce(IUlozeniZbozi_Implementator ulozeniZbozi) {
        this.ulozeniZbozi = ulozeniZbozi;
    }
    public abstract void ulozZbozi(); // implementačně závislé
    public abstract void zmenCenu(double procenta); // implementačně NEzávislé
}
```

_Pivo\_UpresnenaAbstrakce_ stanoví v konstruktoru (dependency injection), kdo bude skutečně externí službu zajišťovat
tuto službu pak volá přes atribut ze své rodičovské třídy - _ulozeniZbozi.uloz(nazev, cena)_

```
public class Pivo_UpresnenaAbstrakce extends AZbozi_Abstrakce {
    private String nazev;
    private double cena;
    public Pivo_UpresnenaAbstrakce(String nazev, double cena,
    IUlozeniZbozi_Implementator ulozeniZbozi) {
        super(ulozeniZbozi);
        this.nazev = nazev;
        this.cena = cena;
    }
    // implementačně závislé
    @Override
    public void ulozZbozi() {
        ulozeniZbozi.uloz(nazev, cena);
    }
    // implementačně NEzávislé
    @Override
    public void zmenCenu(double procenta) {
        cena *= procenta;
    }
}
```

_UlozeniDoCSV\_KonkretniImplementaror_ - první možná implementace služby - uložení do formátu CSV

```
public class UlozeniDoCSV_KonkretniImplementaror implements
    IUlozeniZbozi_Implementator {
    @Override
    public void uloz(String nazev, double cena) {
        System.out.println(nazev + ";" + cena);
    }
}
```

_UlozeniDoXML\_KonkretniImplementaror_ - druhá možná implementace slkužby - uložení do formátu XML

```
public class UlozeniDoXML_KonkretniImplementaror implements
    IUlozeniZbozi_Implementator {
    @Override
    public void uloz(String nazev, double cena) {
        System.out.println("<zbozi>");
        System.out.println(" <nazev>" + nazev + "</nazev>");
        System.out.println(" <cena>" + cena + "</cena>");
        System.out.println("</zbozi>");
    }
}
```

_Sklad\_Aplikace_ využívá možnosti, že každé z piv bude mít jinou externí službu uložení

```
public class Sklad_Aplikace {
    public static void main(String[] args) {
        AZbozi_Abstrakce[] zbozi = new AZbozi_Abstrakce[] {
            new Pivo_UpresnenaAbstrakce("Gambrinus", 10,
             new UlozeniDoXML_KonkretniImplementaror()),
            new Pivo_UpresnenaAbstrakce("Prazdroj", 20,
             new UlozeniDoCSV_KonkretniImplementaror()),
        };
        for (AZbozi_Abstrakce kus : zbozi) {
            kus.zmenCenu(1.5);
            kus.ulozZbozi();
        }
    }
}
```