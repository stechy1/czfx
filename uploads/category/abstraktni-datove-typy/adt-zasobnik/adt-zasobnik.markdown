Obvykle je používán pro dočasné ukládání dat. Operace vyber ze zásonbníku vyjme prvek, který byl operací vlož do zásobníku vložen jako poslední

Pro **zásobník** je charakteristický způsob manipulace s daty - data uložena jako poslední budou čtena jako první. Proto se používá také výraz_LIFO_ z anglického "_Last In, First Out_".

Pro manipulaci s uloženými datovými položkami se udržuje tzv. _ukazatel zásobníku_, který udává relativní adresu poslední přidané položky, tzv. _vrchol zásobníku_.

Obsahem zásobníku mohou být jakékoli datové struktury. Může být realizován jak programovými prostředky, tak i elektronickými obvody.

Důležité pojmy:

*   _push_ - vlož
*   _pop_ - vyber

Navrhněme si jednoduché rozhraní pro zásobník celých čísel:

```
class IntZasobnik {
    IntZasobnik() // vytvoření prázdného zásobníku
    boolean jePrazdny() // test je-li prázdný
    void push(int) // vložení prvku
    int pop() // výběr prvku
}
```

Opět můžeme implementovat dvojím způsobem: pomocí pole a spojovou strukturou.

### Implementace pomocí pole

```
class IntZasobnik {
    private int[] z;
    private int vrchol;
    final int maxN=10;
    IntZasobnik() {
        z = new int[maxN];
        vrchol = 0;
    }
    boolean jePrazdny() {
        return (vrchol == 0);
    }
    void push(int klic) {
        z[vrchol++] = klic;
    }
    int pop() {
        return z[--vrchol];
    }
}
```

Část každé z operací nad zásobníkem implementovaným pomocí pole je O(1).

Vykonáním operace _pop_ nad prázdným zásobníkem vznikne podtečení _underflow_ zásobníku. Vykonáním operace push na plný zásobník vznikne přetečení _overflow_ zásobníku.

Velikost vytvářeného zásobníku zadávaná v parametru konstruktoru:

```
private int[] z;
private int vrchol;
IntZasobnik(int maxN) {
    z = new int[maxN];
    vrchol = 0;
}
```

Mít pevně danou velikost zásobníku by bylo příliš neefektivní - nevíme, kolik položek budeme do zásobníku vkládat. Proto si popíšeme techniku dynamického rozšiřování pole:

```
class DynPole {
    public static void main (String[] arg) {
        int maxN = 4;
        int n;
        int[] a = new int[maxN];
        for(n = 0; n < a.length; n++)
            a[n] = n;

        int[] x = a;

        a = new int[2*a.length];
        for(n = 0; n < x.length; n++)
            a[n] = x[n];

        for(n = maxN; n < a.length; n++)
            a[n] = n;

        for(n=0; n < a.length; n++)
            System.out.println(n);
    }
}
```

Čas vkládání při použití dynamického pole je O(1)

### Implementace spojovým seznamem

```
class IntZasobnik {
    private Prvek vrchol;
    private class Prvek {
        int klic;
        Prvek dalsi;
        Prvek (int klic, Prvek dalsi) {
            this.klic = klic;
            this.dalsi = dalsi;
        }
    }
    IntZasobnik() {
        vrchol = null;
    }
    boolean jePrazdny() {
        return (vrchol == null);
    }
    void push(int klic) {
        vrchol = new Prvek(klic, vrchol);
    }
    int pop() {
        int v = vrchol.klic;
        vrchol = vrchol.dalsi;
        return v;
    }
}
```

Čas každé z operací nad zásobníkem implementovaným pomocí spojového seznamu je O(1)

Implementace **IntZasobnik** můžeme zaměnit bez jakékoliv změny klientského programu.

```
class Zasobnik {
    public static void main(String[] arg) {
        IntZasobnik Zasobnik = new IntZasobnik();

        if (Zasobnik.jePrazdny())
            System.out.println("zasobnik je prazdny");

        Zasobnik.push(4);
        System.out.println(Zasobnik.pop());

        if (Zasobnik.jePrazdny())
            System.out.println("zasobnik je prazdny");

        Zasobnik.push(4);
        Zasobnik.push(3);
        Zasobnik.push(2);
        Zasobnik.push(1);

        System.out.println(Zasobnik.pop());
        System.out.println(Zasobnik.pop());
        System.out.println(Zasobnik.pop());
        System.out.println(Zasobnik.pop());
    }
}
```

#### Zásobník objektů

Pojďme si ještě udělat zásobník obecných objektů. Rozhraní bude podobné jako u **IntZasobnik**.

```
class ObjZasobnik {
    ObjZasobnik()
    boolean jePrazdny()
    void push(Objekt)
    Objekt pop()
}
```

Implementace **ObjZasobniku**:

```
class ObjZasobnik {
    private Prvek vrchol;
    private class Prvek {
        Objekt ref;
        Prvek dalsi;
        Prvek (Objekt ref, Prvek dalsi) {
            this.ref = ref;
            this.dalsi = dalsi;
        }
    }
    ObjZasobnik() {
        vrchol = null;
    }
    boolean jePrazdny() {
        return (vrchol == null);
    }
    void push(Objekt ref) {
        vrchol = new Prvek(ref, vrchol);
    }
    Objekt pop() {
        Objekt r = vrchol.ref;
        vrchol = vrchol.dalsi;
        return r;
    }
}
```

Méně vhodné řešení je rozšířit ukládaný objekt o členskou proměnnou _dalsi_, protože vyžaduje zásah do deklarace třídy ukládaných objektů.

### Porovnání implementací

Implementace pomocí pole - vyžaduje po celou dobu vypočtu pameť pro předpokládaný maximální počet prvků (pokud neuvažujeme dynamické pole)

Implementace pomocí spojového seznamu - vyžaduje paměťový prostor úměrný počtu uložených prvků. Potřebuje paměť pro uchování ukazatelů a čas na přidělení paměti při každé operaci _push_ a nakonec čas na uvolnění paměti po každé operaci _pop_.