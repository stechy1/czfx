Zavádí vztah 1 : N (pozorovaný : pozorovatelé) mezi objekty reagujícími na změnu stavu pozorovaného nebo na jím sledované události
- pozorovatelé se u pozorovaného jednorázově přihlásí
- pozorovaný je průběžně informuje
- často je k dispozici i možnost odhlášení pozorovatele ze seznamu

Komunikace je **jednosměrná**.

Jiné názvy vzoru - Posluchač (Listener), Vydavatel\-Předplatitel (Publisher-Subscriber).
Pro základní využití výhod tohoto návrhového vzoru používáme knihovní dvojici Observer-Observable
- logicky chybné (prohozené) pojmenování
Pozorovany musí dědit _Observable_ (nevýhoda)
- pak má k dispozici:
 - _setChanged()_ - nastala událost, o které se vyplatí informovat pozorovatele
 - _notifyObservers(Object data)_ \-informování o událostí, kde _data_ jsou možné upřesnění reportované událost
  - nechceme\-li upřesňovat, pak null

```
import java.util.Observable;
public class Pozorovany extends Observable {
    public void posliNovouZpravu(String novaZprava) {
        setChanged();
        notifyObservers(novaZprava);
    }
}
```

_Zapisovatel\_Pozorovatel_ implementuje jedinnou metodu z Observer - _update(Observable obs, Object data)_
- _obs -_ umožňuje posluchači identifikovat zdroj události - principiálně může pozorovat víc zdrojů
- _data_ - upřesňující zpráva

```
import java.util.Observable;
import java.util.Observer;
public class Zapisovatel_Pozorovatel implements Observer {
    @Override
    public void update(Observable obs, Object data) {
        System.out.println("Zapisuji zprávu: " + data);
        }
}
```

_Archivator\_Pozorovate\_l_ další typ pozorovatele

```
import java.util.Observable;
import java.util.Observer;
public class Archivator_Pozorovatel implements Observer {
    @Override
    public void update(Observable obs, Object data) {
     System.out.println("Archivuji zprávu: " + data);
    }
}

```

_Informator\_Pozorovatel_ další typ pozorovatele

```
import java.util.Observable;
import java.util.Observer;
public class Informator_Pozorovatel implements Observer {
    @Override
    public void update(Observable obs, Object data) {
        System.out.println("Informuji o zprávě: " + data);
    }
}

```

```
public class PredavaniNovychZprav_Aplikace {
    public static void main(String[] args) {
        Pozorovany vysilac = new Pozorovany();
        // vytváření a registrace posluchačů
        vysilac.addObserver(new Zapisovatel_Pozorovatel());
        vysilac.addObserver(new Informator_Pozorovatel());
        vysilac.addObserver(new Archivator_Pozorovatel());
        // zaslání zprávy všem registrovaným
        vysilac.posliNovouZpravu("Začínáme");
        vysilac.posliNovouZpravu("Pokračujeme");
        vysilac.posliNovouZpravu("Končíme");
    }
}

```

Výpis do konzole:

```
Archivuji zprávu: Začínáme
Informuji o zprávě: Začínáme
Zapisuji zprávu: Začínáme
Archivuji zprávu: Pokračujeme
Informuji o zprávě: Pokračujeme
Zapisuji zprávu: Pokračujeme
Archivuji zprávu: Končíme
Informuji o zprávě: Končíme
Zapisuji zprávu: Končíme

```