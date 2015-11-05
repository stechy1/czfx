Motivace:

Mějme jeden seznam chlapců a dívek. Vytiskněte tyto seznamy tak, aby v levém sloupci byli pod sebou jména všech chlapců a v pravém sloupci jména všech dívek.

V seznamu je stejný počet dívek a chlapců a byli do něj vloženi náhodně.

![Motivace](../uploads/category/abstraktni-datove-typy/adt-iterator-seznamu/attachments/motivace.png)

Algoritmus (myšlenka)

*   najdi prvního chlapce a vypiš jméno
*   najdi první dívku a vypiš jméno
*   přejdi na nový řádek

*   najdi dalšího chlapce a vypiš jméno
*   najdi další dívku a vypiš jméno
*   přejdi na nový řádek
*   skončí, neni-li v seznamu další chlapec ani další dívka.

Implementace:

Oddělíme data objektu implementující **seznam** - proměnná _první_ a práci se samotným seznamem - proměnné _nynejsi_ a _predch_, implementuje **iterátor**.

Objekt třídy **Seznam** poskytuje metodu k získání iterátoru _getIterator()_

```
class Seznam {
    private Prvek prvni;
    ...
    IteratorSeznamu getIterator() {
    return new IteratorSeznamu(this);
    }
    ...
}
```

```
class IteratorSeznamu {
    private Prvek nynejsi;
    private Prvek predch;
    private Seznam s;
    IteratorSeznamu(Seznam s) {
        this.s = s;
        naZacatek();
    }
    ...
}
```

Každý iterátor spravuje vlastní okamžitou pozici v seznamu.

```
public static void main (...) {
    ...
    Seznam nasSeznam = new Seznam();
    IteratorSeznamu it1 = nasSeznam.getIterator();
    IteratorSeznamu it2 = nasSeznam.getIterator();
}
```

![Popis získání iterátoru](../uploads/category/abstraktni-datove-typy/adt-iterator-seznamu/attachments/popis-iteratoru.png)

Definujme tedy nové rozhraní pro náš seznam:

```
class Seznam {
    Seznam()
    Prvek getPrvni()
    void setPrvni(Prvek)
    boolean jePrazdny()
    IteratorSeznamu getIterator()
    void tiskSeznamu ()
}
```

Dále definujme rozhraní pro IteratorSeznamu:

```
class IteratorSeznamu {
    IteratorSeznamu(Seznam)
    void naZacatek()
    boolean jePosledni()
    void naDalsiPrvek()
    int ctiKlic()
    void vloz(int)
    int vyber
}
```

Implementace metod tříd **Seznam** a **IteratorSeznamu**

```
class Prvek {
    int klic;
    Prvek dalsi;
    Prvek (int klic) {
        this.klic = klic;
    }
    void tiskPrvku() {
        System.out.print(klic + " ");
    }
}
```

```
class Seznam {
    private Prvek prvni;
    Seznam() {
        prvni = null;
    }
    Prvek getPrvni() {
        return prvni;
    }
    void setPrvni(Prvek ref) {
        prvni = ref;
    }
    boolean jePrazdny() {
        return (prvni == null);
    }
    IteratorSeznamu getIterator() {
        return new IteratorSeznamu(this);
    }
    void tiskSeznamu() {
        for (Prvek x = prvni; x != null; x = x.dalsi)
            x.tiskPrvku();
        System.out.println("");
    }
}
```

```
class IteratorSeznamu {
    private Prvek nynejsi;
    private Prvek predch;
    private Seznam s;
    IteratorSeznamu(Seznam s) {
        this.s = s;
        naZacatek();
    }
    void naZacatek() {
        nynejsi = s.getPrvni();
        predch = null;
    }
    boolean jePosledni() {
        return (nynejsi.dalsi == null);
    }
    void naDalsiPrvek() {
        predch = nynejsi;
        nynejsi = nynejsi.dalsi;
    }
    int ctiKlic() {
        return nynejsi.klic;
    }
    void vloz(int i) {
        Prvek novy = new Prvek(i);
        if (s.jePrazdny()) {
            s.setPrvni(novy);
            nynejsi = novy;
        } else {
            novy.dalsi = nynejsi.dalsi;
            nynejsi.dalsi = novy;
            naDalsiPrvek();
        }
    }
    int vyber() {
        int i = nynejsi.klic;
        if(predch == null) {
            s.setPrvni(nynejsi.dalsi);
            naZacatek();
        } else {
            predch.dalsi = nynejsi.dalsi;
            if (jePosledni())
            naZacatek();
        else
            nynejsi = nynejsi.dalsi;
        }
    return i;
    }
}
```

V implementaci některých operací musíme rozlišovat, je-li okamžitá pozice první prvek nebo ne. Řešení - hlavička seznamu.

### Hlavička seznamu

Odkaz na první prvek seznamu je v položce _dalsi_ v zdánlivém (dummy) prvku seznamu - v hlavičce.

![Hlavička seznamu](../uploads/category/abstraktni-datove-typy/adt-iterator-seznamu/attachments/hlavicka-seznamu.png)

_hlavicka_ nahradí proměnnou _prvni_

Tedy ve třídě **Seznam** musíme provést následující změny:

při inicializaci:

```
hlavicka = new Prvek();
```

test je-li prázdný:

```
if (hlavicka.dalsi == null)
```

_getHlavicka()_ bude vracet referenci na hlavičku seznamu

Třída **IteratorSeznamu**

nastavení na začátek:

```
if(s.jePrazdny()) {
    nynejsi = s.getHlavicka();
    predch = null;
} else {
    nynejsi = s.getHlavicka().dalsi;
    predch = s.getHlavicka();
}
```

vlož prvek za nynější:

```
novy.dalsi = nynejsi.dalsi;
nynejsi.dalsi = novy;
naDalsiPrvek();
```

vyber nynější:

```
predch.dalsi = nynejsi.dalsi;
if (jePosledni())
    naZacatek();
else
    nynejsi = nynejsi.dalsi;
```