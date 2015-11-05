Co je prioritní fronta?

Definována operacemi:

*   vlož prvek
*   vyber největší (nejmenší) prvek

Proč pf?

**Rozhraní**

```
class PF {
    // ADT rozhrani
    PF(); // vytvoření prázdné prioritní fronty
    boolean jePrazdna(); // test, je-li prázdná
    void vloz(Prvek); // vložení prvku
    Prvek vybermax(); // výběr největšího prvku
}
```

další možné operace - nalezení, přečtení největšího prvku.

### Implementace prioritní fronty pomocí pole

**Myšlenka:** pole je uspořádáno vzestupně
1. **vyberMax** - odebere poslední prvek :-)
2. **vlož** - větší prvky posune doprava o jednu pozici :-(

```
class PF {
    private int[] pf;
    private int pocet;
    final int maxN=10;
    PF() {
        pf = new int[maxN];
        pocet = 0;
    }
    boolean jePrazdna() {
        return (pocet == 0);
    }
    void vloz(int klic) {
        if (pocet == 0)
            pf[0] = klic;
        else { // hledame index vlozeni i
            int i = pocet;
            while (i > 0 && pf[i - 1] > klic) {
                pf[i] = pf[i - 1];
                --i;
            }
        pf[i] = klic;
        }
        pocet++;
    }
    int vybermax() {
        return pf[--pocet];
    }
}
```

**Jiná myšlenka:** pole prvků není uspořádáno
1\. vyberMax - najdi největší :-(
2\. vlož - přidej na konec :-)

```
class PF {
    private int[] pf;
    private int pocet;
    final int maxN=10;
    PF() {
        pf = new int[maxN];
        pocet = 0;
    }
    boolean jePrazdna() {
        return (pocet == 0);
    }
    void vloz(int klic) {
        pf[pocet++] = klic;
    }
    int vybermax() {
        int max = 0;               //index max prvku
        for (int i = 1; i < pocet; i++)
            if (pf[max] < pf[i]) max =i;
                int t = pf[max];   //vymenime max a
        pf[max] = pf[pocet - 1];   //posledni
        pf[pocet - 1] = t;
        return pf[--pocet];
    }
}
```

### Implementace prioritní fronty pomocí spojového seznamu

**Myšlenka:** pomůže neuspořádaný seznam?
1. **vyberMax** - najdi největší :-(
2. **vlož** - přidej na začátek seznamu :-)

```
class PF {
    private class Prvek {
        int klic;
        Prvek dalsi;
        Prvek predch;
        Prvek() {}
        Prvek(int klic) {
            this.klic = klic;
            this.dalsi = null;
            this.predch = null;
        }
    }
    private Prvek hlavicka;
    PF () {
        hlavicka = new Prvek();
        hlavicka.dalsi = hlavicka;
        hlavicka.predch = hlavicka;
    }
    boolean jePrazdna() {
        return (hlavicka.dalsi == hlavicka.dalsi.dalsi);
    }
    void vloz(int klic) {
        Prvek novy = new Prvek(klic);
        novy.dalsi = hlavicka.dalsi;
        novy.predch = hlavicka;
        hlavicka.dalsi.predch = novy;
        hlavicka.dalsi = novy;
    }
    int vybermax() {
        Prvek x = hlavicka.dalsi;
        for (Prvek t = x.dalsi; t != hlavicka;
            t = t.dalsi)
        if (x.klic < t.klic) x = t;
            //x ukazatel na max prvek
            int max = x.klic;
        x.predch.dalsi = x.dalsi;
        x.dalsi.predch = x.predch;
        return max;
    }
}

```

**Jiná myšlenka:** pomůže uspořádaný seznam?
1. **vyberMax** - vyber prvek ze začátku seznamu :-)
2. **vlož** - vlož před _první menší_ prvek nebo na _konec_ :-(

### Složitost:

 vlož | vyber největší prvek | najdi největší prvek |
| uspořádané pole | N | 1 | 1 |
| uspořádaný seznam | N | 1 | 1 |
| neuspořádané pole | 1 | N | N |
| neuspořádaný seznam | 1 | N | N |

Jakou implementaci zvolíme, potřeujeme-li v aplikaci často zjistit největší prvek a zřídka vložit prvek?

trpělivý (lazy přístup) vs. netrpwlivý (eager) přístup
neuděláme co nemusíme a máme príci později vs. uděláme hned co můžeme a později máme klid
(pouze vložíme) vs. (vložíme a uspořádáme)

### Implementace prioritní fronty pomocí BVS (binární vyhledávací strom)

**Myšlenka** operace vložení a nalezení prvku se zadaným klíčem byly **O(h)**, kde _h_ je výčka stromu
1. **vyberMax**

```
int vybermax() {
    DVrchol x = koren;
    DVrchol predch = null;
    while (x.pravy != null) {
        predch = x;
        x = x.pravy;
    }
    if (x == koren)
        koren = koren.levy;
    else
        predch.pravy = x.levy;
    return x.klic;
}
```

![Stav BVS před výběrem maximálního prvku](../uploads/category/abstraktni-datove-typy/adt-prioritni-fronta-halda-razeni/attachments/prioritni-fronta-bvs-vybermax-pred.png)

![Stav BVS po výběru maximálního prvku](../uploads/category/abstraktni-datove-typy/adt-prioritni-fronta-halda-razeni/attachments/prioritni-fronta-bvs-vybermax-po.png)

Operace vložení, nalezení i výběru největšího prvku jsou O(h). Nejhorší případ nastane, když h = N-1\. Průměrný případ h = 1, 39log<sub>2</sub>N

**Umíme to zlepšit?**

## Halda (heap)

**Myšlenka:** N prvků můžeme uložit v úplném binárním stromě s výškou

h = Θ(log<sub>2</sub>N)

**Vlastnost haldy** - klíč v každém vrcholu je větší nebo roven klíčům v jeho následnících, pokud je má. Kořen je největší prvek.

Halda je úplný binární strom s vlastností haldy reprezentován pmocí pole.
Přesněji _max-halda_, obdobně _min-halda_.

Nalezení největšího prvku je O(1), protože největší prvek je první prvek pole.

**1\. vyberMax - **Vybereme kořen (první prvek pole) a nahradíme ho posledním prvkem. Strom zůstal úplným binárním stromem, ale mohla být porušena vlastnost haldy. Obnovení vlastnosti haldy: nový kořen vyměníme s větším z jeho následníků, ..., dokud není obnovena vlastnost haldy

```
// kořen haldy má index 1
// poslední prvek má index pocet
// metoda dolu() začne obnovu haldy od indexu k
// pro vybermax bude k=1
private void dolu(int k, int pocet) {
    // levy následník má index 2k
    while (2*k <= pocet) {
        int j = 2*k;
        // exituje-li pravý následník j < pocet,
        // j bude index většího z obou následníků
        if (j < pocet && pf[j] < pf[j+1]) j++;
        if (pf[k] >= pf[j]) break; // obnoveno
        vymen(k,j);
        k = j;
    }
}
```

![Ukázka posunutí prvku směrem dolu](../uploads/category/abstraktni-datove-typy/adt-prioritni-fronta-halda-razeni/attachments/posun-v-halde-dolu.png)

**2\. vloz** - přidáme prvek na konec pole. Mohla být porušena vlastnost haldy. Obnovení vlastnosti haldy: vyměníme vložený prvek s předchůdcem, ..., dokud není obnovena vlastnost haldy.

```
// metoda nahoru() začne obnovu haldy od indexu k
// pro vloz bude k=pocet
private void nahoru(int k) {
    // předchůdce má index k/2
    while (k > 1 && pf[k/2] < pf[k]) {
        vymen(k, k/2);
        k = k/2;
    }
}
```

![Ukázka posunutí prvku směrem nahoru](../uploads/category/abstraktni-datove-typy/adt-prioritni-fronta-halda-razeni/attachments/posun-v-halde-nahoru.png)

```
class PF {
    private void dolu ... ;
    private void nahoru ... ;
    private void vymen ... ;
    private int[] pf;
    final int maxN = 10;
    private int pocet;
    PF() {
        pf = new int[maxN + 1];
        pocet = 0;
    }
    boolean jePrazdna() {
        return pocet == 0;
    }
    void vloz(int klic) {
        pf[++pocet] = klic;
        nahoru(pocet);
    }
    int vybermax() {
        vymen(1, pocet);
        dolu(1, pocet - 1);
        return pf[pocet--];
    }
}
```

### Složitost

vyberMax - potřebuje nejvíce 2log<sub>2 </sub>N porovnání
vlož - potřebuje nejvíce log<sub>2 </sub>N porovnání

operace jsou O(log N)

Vytvoření haldy s N prvky postupným vkládáním prvků operací **vlož** v nejhorším případě je:

log<sub>2 </sub>N + ... + log<sub>2 2</sub> + log<sub>2 1</sub> < N log<sub>2 </sub>N

## Řazení a prioritní fronta

```
class RazeniPole {
    //ADT rozhrani
    void nactiPrvek(int)
    void tiskPole()
    void razeniPF()
}
```

**Implementace** metod _nactiPrvek()_ a _tiskPrvku()_

```
class RazeniPole {
    private int[] pole;
    private int pocet;
    final int maxN;
    RazeniPole() {
        pole = new int[maxN];
        pocet = 0;
    }
    void nactiPrvek(int klic) {
        pole[pocet] = klic;
        pocet++;
    }
    void tiskPole() {
        for(int i = 0; i < pocet; i++)
            Sytem.out.print(pole[i]+ " ");
        System.out.println(" ");
    }
}
```

**Myšlenka:**
1\. vytvoříme prioritní frontu
2\. vybíráme největší prvek a ukládáme do konce původního pole

```
void razeniPF() {
    PF pf = new PF();
    int i;
    // z pole vložíme prvky do prioritní fronty
    for(i = 0; i < pocet; i++)
        pf.vloz(pole[i]);
    // do pole je uložíme operací vybermax
    for(i = pocet - 1; i >= 0; i--)
        pole[i] = pf.vybermax();
}
```

Metoda _razeniPF()_ obecně neřadí na místě.
**PF uspořádaným polem** vede na metodu řazení typu řazení vkládáním.
**PF neuspořádaným polem** vede na metodu řazení, která odpovídá řazení výběrem.

### Implementace prioritní fronty pomocí hlady

**Myšlenka:**
1\. v poli vytvoříme haldu o dvou, třech, ..., _pocet_ prvcích
2\. největší prvek vyměníme s posledním a obnovíme haldu o jeden prvek menší

```
class RazeniPoleHaldou1 {
    private void vymen(int i, int j) {
        int t = pf[i];
        pf[i] = pf[j];
        pf[j] = t;
    }
    private void dolu(int k, int pocet) {
        while (2*k <= pocet) {
            int j = 2*k;
            if (j < pocet && pf[j] < pf[j+1]) j++;
            if (pf[k] >= pf[j]) break;
                vymen(k,j);
            k = j;
        }
    }
    private void nahoru(int k) {
        while (k > 1 && pf[k/2] < pf[k]) {
            vymen(k, k/2);
            k = k/2;
    }
}
```

### Složitost

Vytvoření - N log<sub>2 </sub>N
Vybírání největšího prvku - log<sub>2 </sub>N + ... + log<sub>2</sub> 2 +log<sub>2</sub> 1 < N log<sub>2 </sub>N
Řazení je N log<sub>2 </sub>N