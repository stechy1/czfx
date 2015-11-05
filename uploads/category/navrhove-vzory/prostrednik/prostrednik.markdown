Používá se v případě, že má vzájemně komunikovat větší množství objektů.

Nelze, aby komunikoval každý objekt s každým - mnoho vazeb - zdroj chyb, problémy při budoucích úpravách.

Odstraňuje vzájemné vazby mezi množinou navzájem komunikujících objektů tím, že zavede objekt, který bude prostředníkem mezi komunikujícími objekty (telefonní ústředna)
- tím se ruší vzájemná přímá závislost mezi jednotlivými objekty

Prostředník pro svoji implementaci často využívá návrhový vzor **Vysílač - Posluchač**

Objekt, který chce předat zprávu jiným, ji předá _Vysílači_, u kterého jsou jiní zaregistrovaní jako _Posluchači._
- _Vysílač jim zprávu předá._

Prostředník definuje formát zpráv, které je mu možno posílat.
- Nejjednodušší formou je zpráva bez parametrů, která se zašle všem.
- Zpráva ale může mít parametr (např. telefonní číslo, pozici, atd..) ze které může _Prostředník_ s použitím další libovolné sofistikované logiky určit adresáta nebo omezenou skupinu adresátů.

Komunikace je **obousměrná**.

_Prostrednik_ využije všech možností _java.util.Observable_

```
import java.util.Observable;
public class Prostrednik extends Observable {
    public void predejZpravu(String zprava) {
        setChanged();
        notifyObservers(zprava);
    }
}

```

GrafickyObjekt využívá možností java.util.Observer
- _kdoJsem_ - je unikátní pojmenování (ID) příjemce
- příslušný prostředník je předán v konstruktoru (_dependency injection_)
- _update()_ - přijímá zprávy od prostředníka a dál je filtruje, zda je zpráva určená pro něj
- _posliZpravu()_ - posílá svoji novou zprávu prostředníkovi a ten zajistí její distribuci všem registrovaným

```
import java.util.Observable;
import java.util.Observer;
public class GrafickyObjekt implements Observer {
    private Prostrednik prostrednik;
    private String kdoJsem;
    public GrafickyObjekt(Prostrednik prostrednik, String kdoJsem) {
        this.prostrednik = prostrednik;
        this.kdoJsem = kdoJsem;
    }
    @Override
    public void update(Observable obs, Object novaZprava) {
        String zprava = (String) novaZprava;
        String prijemce = zprava.substring(0, 1).toUpperCase();
        if (kdoJsem.equals(prijemce) || prijemce.equals("*")) {
         System.out.println(kdoJsem + " děkuje za zprávu: " + zprava.substring(1));
        }
    }
    // komu = "*" -> vsem
    public void posliZpravu(String komu, String zprava) {
        prostrednik.predejZpravu(komu + zprava);
    }
}

```

```
public class PredavaniZprav_Aplikace {
    public static void main(String[] args) {
        Prostrednik prostrednik = new Prostrednik();
        GrafickyObjekt a = new GrafickyObjekt(prostrednik, "A");
        GrafickyObjekt b = new GrafickyObjekt(prostrednik, "B");
        GrafickyObjekt c = new GrafickyObjekt(prostrednik, "C");
        // vytváření a registrace posluchačů
        prostrednik.addObserver(a);
        prostrednik.addObserver(b);
        prostrednik.addObserver(c);
        // zaslání zprávy všem registrovaným
        a.posliZpravu("b", "Přejdi do pozadí");
        a.posliZpravu("*", "Překreslete se");
        b.posliZpravu("a", "Nezobrazuji se");
        c.posliZpravu("b", "Překresli se");
    }
}

```

Vypíše na konzoli:

```
B děkuje za zprávu: Přejdi do pozadí
C děkuje za zprávu: Překreslete se
B děkuje za zprávu: Překreslete se
A děkuje za zprávu: Překreslete se
A děkuje za zprávu: Nezobrazuji se
B děkuje za zprávu: Překresli se

```