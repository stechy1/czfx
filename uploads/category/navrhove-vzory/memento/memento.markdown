Zabezpečuje uchovávání stavů objektů, aby je bylo možné v případě potřeby uvést do původního stavu
- přitom zajišťuje, že při uchování stavu není narušeno ukrytí implementace
základní princip operací _Undo_ (Ctrl Z) a _Redo_ (Ctrl Y)
_StavUctu\_Memento_ - třída pamatující si stav
- stav je uložen v atributech, kterých je potřebný počet - zde jen _stavUctu_

```
public class StavUctu_Memento {
    private final int stavUctu;
        public StavUctu_Memento(int stavUctu) {
        this.stavUctu = stavUctu;
    }
    public int vratUlozenyStav() {
        return stavUctu;
    }
}

```

_Ucet\_Originator_ - třída, jejíž stav se bude měnit a budeme jej ve vhodných okamžicích uchovávat
- třída má běžné metody
- _StavUctu\_Memento_ _ulozStavDoMementa()_ - operace pro uložení aktuálního stavu
 - je využívána z vnějšku třídy _Ucet_Originator_
  - tím je možná volba počtu uložených stavů, místa uložení apod., nezávisle na _Ucet_Originator_
- _obnovZMementa(StavUctu\_Memento memento)_ - obnovení do jednoho z uložených stavů
- termín "Originator" označuje v definici tohoto návrhového vzoru tu třídu, která využívá memento

```
public class Ucet_Originator {
    private int stavUctu;
    // operace s účtem
    public void setStavUctu(int stavUctu) {
        System.out.println("Účet: nastavení stavu na: " + stavUctu);
        this.stavUctu = stavUctu;
    }
    public int getStavUctu() {
        return stavUctu;
    }
    public int vyber(int kolik) {
        setStavUctu(stavUctu - kolik);
        return kolik;
    }
    public void uloz(int kolik) {
        setStavUctu(stavUctu + kolik);
    }
    // operace s Mementem
    public StavUctu_Memento ulozStavDoMementa() {
        System.out.println("Účet: ukládání stavu " + stavUctu + " do Mementa.");
        return new StavUctu_Memento(stavUctu);
    }
    public void obnovZMementa(StavUctu_Memento memento) {
        stavUctu = memento.vratUlozenyStav();
        System.out.println("Účet: Stav po obnovení z Mementa: " + stavUctu);
    }
}

```

_PohybyNaUctu\_Aplikace_ - ukládá si stavy do seznamu
- tak by bylo možné provést libovolně dlouhou sekvenci kroků _Undo_ a/nebo _Redo_

```
public class PohybyNaUctu_Aplikace {
    public static void main(String[] args) {
        List<StavUctu_Memento> ulozeneStavy = new ArrayList<StavUctu_Memento>();
        Ucet_Originator ucet = new Ucet_Originator();
        ucet.setStavUctu(1000);
        ulozeneStavy.add(ucet.ulozStavDoMementa());
        ucet.uloz(2000);
        ulozeneStavy.add(ucet.ulozStavDoMementa());
        ucet.vyber(4000);
        if (ucet.getStavUctu() < 0) {
            ucet.obnovZMementa(ulozeneStavy.get(1));
            System.out.println("Nedostatek hotovosti - výběr nebyl proveden");
        }
        System.out.println("Stav účtu: " + ucet.getStavUctu());
    }
}

```

Výpis do konzole:

```
Účet: nastavení stavu na: 1000
Účet: ukládání stavu 1000 do Mementa.
Účet: nastavení stavu na: 3000
Účet: ukládání stavu 3000 do Mementa.
Účet: nastavení stavu na: -1000
Účet: Stav po obnovení z Mementa: 3000
Nedostatek hotovosti - výběr nebyl proveden
Stav účtu: 3000
```