## Motivace

| Příjmení | Startovní číslo |
| --- | --- |
| ADAM | 1 |
| DOLEJŠ | 2 |
| HESOUN | 3 |
| JANDÁK | 4 |
| ŠANTALA | 5 |
| ŠEFLOVÁ | 6 |
| ŠINDEL | 7 |
| ŠNAJDR | 8 |
| ŠONKA | 9 |
| ŠOTOLA | 10 |
| ŠTĚPÁN | 11 |
| TICHÝ | 12 |
| TICHÝ | 16 |
| ŤOUPALÍK | 14 |
| VÁPENÍK | 15 |
| VINTÍŘ | 16 |
| VYDRÁK | 17 |
 18 |
| ZDVOŘILÝ | 19 |
| ŽEBRÁK | 20 |

Z tabulky je vidět, že 18\. závodník nenastoupil.
_příjmení_ - prvek; _startovní číslo_ - klíč
Otázka zní: _Jak se jmenuje závodník se startovním číslem 11?_

## Možnosti implementace

*   spojový seznam (O(n))
*   seřazené pole podle klíče (O(log n))
*   BVS (O(log n))

Jde to rychleji? O(1)?
Ano, jde to rychleji a to hned několika způsoby.

## Tabulka s přímým adresováním - O(1)

Prvek uložíme do prvku pole s indexem rovným klíči ukládaného prvku.

 Příjmení | Startovní číslo |
| --- | --- | --- |
| 1 | ADAM | 1 |
| 2 | DOLEJŠ | 2 |
| 3 | HESOUN | 3 |
| 4 | JANDÁK | 4 |
| 5 | ŠANTALA | 5 |
| 6 | ŠEFLOVÁ | 6 |
| 7 | ŠINDEL | 7 |
| 8 | ŠNAJDR | 8 |
| 9 | ŠONKA | 9 |
| 10 | ŠOTOLA | 10 |
| 11 | ŠTĚPÁN | 11 |
| 12 | TICHÝ | 12 |
| 16 | TICHÝ | 16 |
| 14 | ŤOUPALÍK | 14 |
| 15 | VÁPENÍK | 15 |
| 16 | VINTÍŘ | 16 |
| 17 | VYDRÁK | 17 |
| 18 | 18 |
| 19 | ZDVOŘILÝ | 19 |
| 20 | ŽEBRÁK | 20 |

**Klíče** všech prvků jsou **různé**. **Klíče** jsou z množiny **K** o velikosti **|K|**. Každému klíči odpovídá prvek pole **Prvek[] t**, **t.length = |K|**, v tabulce je místo pro každý "klíč". Množina klíčů aktuálně uložených prvků **A⊆****K, |A|≤|K|**.

**Příklad:
**Mějme startující se startovními čísli 1 až 365, potom **K** = {1, 2, ..., 365}, |K| = 365\. Pro tabulku by platilo, že **t.length** = |K| = 365\. Řekněme, že startující s čísli 3, 101 a 364 ke startu nenastoupili, potom **A** = {1, 2, 4, ..., 363, 365}, |A| = 362.

Lépe popisující obrázek:

![Příklad tabulky s přímým adresováním](../uploads/category/abstraktni-datove-typy/adt-hash-table/attachments/tabulka-s-primym-adresovanim.png)

### Implementace

```
class Prvek {
    int klic;
    String data;
    ...
    Prvek(int klic, String data) {
        this.klic = klic;
        this.data = data;
    }
}
```

```
class Tabulka {
    // rozhrani ADT
    Tabulka()
    Prvek hledej(int)
    vloz(Prvek)
    vyber(Prvek)
}
```

```
class Tabulka {
    Prvek[] t = new Prvek[│K│];
    Tabulka() {
    for(int i = 0; i < t.length; i++) {
        t[i] = null; // prázdná tabulka
    }
    Prvek hledej(int k) {
        return t[k];
    }
    void vloz (Prvek x) {
        t[x.klic] = x;
    }
    void vyber (Prvek x) {
        t[x.klic] = null;
    }
}
```

Všechny operace jsou rychlé, čas je O(1). Vhodné, není-li velikost |K| množiny všech klíčů **K** velká.

**Poznámky:**

Alternativní implementace by mohla namísto referencí na prvky dynamické množiny v poli implementujícím tabulku obsahovat přímo samotné prvky.

Klíč prvků známe na základě indexu a nemusíme jej uchovávat v prvku samotném. Musíme ovšem být schopni poznat, že prvek pole je prázdný.

Operace vloz a vyber mají jako parametr ukazatel na prvek množiny. Důvodem takové konstrukce může být existence prvků množiny před jejich organizací v tabulce.

Příkladem může být pole všech přihlášených závodníků, ale do tabulky jejich umístnění a dosáhnutého výkonu vložíme jenom ty závodníky, kteří nastoupili a závod dokončili. Obdobně, byl-li některý závodník diskvalifikován nebude se nacházet v tabulce výsledků.

## Rozptylová (hash) tabulka

**Problém**, je-li |A| << |K| tak za přístup **O(1)** k prvkům (nalezení, vložení, vybírání) platíme nízkým využítím paměti |A|/|K|

**Řešení**
namísto t.length = **|K|** volíme t.length ∼ **|A|**. Tím využijeme paměť na téměř 100%

Označme _m = t.length_, indexy 0, 1, ..., m-1, m ∼ |A|. Neznámé hodnoty klíčů v A ⊆ K, nejspíš nebudou 0, 1, ..., m-1\. Pro přístup **O(1)** musíme určit hodnotu indexu v tabulce _t[]_ pro každý klíč **k∈K**:

![Rozptylová tabulka](../uploads/category/abstraktni-datove-typy/adt-hash-table/attachments/rozptylova-tabulka.png)

**h: K → {0, 1, ..., m-1}**

kde _h_ je rozptylová funkce (hash function), _h(k)_ je index klíče **k** v tabulce.

m ∼ |A| << |K|

Rozptylová funkce může zobrazit alespoň dva různé klíče na stejný index - **kolize; **pro u≠v a u,v∈K bude h(u) = h(v).

Ideálně jsou všechny klíče prvků množiny **A** jsou zobrazeny na **různé hodnoty indexů**, musí být m ≥ |A|. V nejhorším případě jsou všechny klíče množiny A prvků zobrazeny na **jeden index**.

### Řešení kolize - Vnější řetězení (separate chaining)

Vytvoří se seznam prvků, jejichž klíče jsou zobrazeny na stejnou pozici v tabulce (index).

![Rozptylová tabulka s vnějším řetězením](../uploads/category/abstraktni-datove-typy/adt-hash-table/attachments/tabulka-vnejsi-retezeni.png)

#### **Implementace**

```
class Prvek {
    int klic;
    String data;
    Prvek dalsi;
    Prvek predch; //ulehčí výběr prvku
    Prvek(int klic, String data) {
        this.klic = klic;
        this.data = data;
    }
}
```

Operace **hledej**, **vloz**, **vyber** jsou převedeny na operace nad seznamem s hlavičkou t[h(k)]

```
Prvek hledej(int k) {
	hledej prvek s klíčem k v seznamu t[h(k)]
}
void vloz (Prvek x) {
	vlož prvek x na začátek seznamu t[h(x.klic)]
}
void vyber (Prvek x) {
vyber prvek x ze seznamu t[h(x.klic)]
}
```

Operace vložení prvku do tabulky je O(1). Prvek vkládáme na **začátek** odpovídajícího seznamu. Výhodné pro aplikace s vnořenými strukturami.

```
{int identifikator1 ...
	{int identifikator2 ...
	} ...
}
```

Operace odebrání prvku je O(1).

Operace hledej vyžaduje analýzu - kolik je prvků v seznamu před hledaným klíčem.

#### **Analýza**

Vznik kolizí

*   Pro **|A|** = 1, kolize nemůže vzniknout.
*   Pro **|A|** ≤ m, kolize nemusí vzniknout. Všechny prvky mohou být vloženy na **stejnou pozici** v tabulce a **vytvoří seznam délky |A|**.
*   Pro **|A|** > m, bude alespoň jeden seznam obsahovat více než jeden prvek a kolize musí nastat.

Známe-li, nebo umíme alespoň odhadnout počet prvků n, které chceme uložit v tabulce, jak zvolíme její celikost m?

*   Pokud je **kritická rychlost hledání** volíme **m > n**, za cenu větších nároků na paměť, když alespoň m-n pozic v tabulce bude nevyužitých.
*   V opačném případě volíme **m < n**, kdy se **úměrně prodlouží** průměrný čas hledání.

Obecně se doporučuje zvolit **m = n** a i když nakonec bude nutno vložit více prvků, se vlastnosti výrazně nezhorší.

### Rozptylovací funkce

**h: K → {0, 1, ..., m-1}**

Očekáváme, že každý klíč se zobrazí (přibližně) stejně pravděpodobně na libovolnou z m hodnot, nezávsle na tom, kam je zobrazen jakýkoliv jiný klíč.
Musíme znát charakteristiku výskytu klíčů v aplikaci což není vždycky splněno.
Klíče mohou mít různy typ, od jednoduchých datových typů až ke strukturovaným datovým typům, jakými jsou například objekty.
Každý typ klíče je v počítači reprezentován řetězcem **0** a **1**, který můžeme vyjádřit **jako** (možná velice velké) **celé číslo**.

### Modulární metoda

**Klíčem je číslo.**

Hodnoty **h(k) = k mod m** (v Javě k % m) jsou právě z množiny {0, 1, ..., m-1}. m nemůžeme volit libovolně, je-li m = 2<sup>p</sup>, potom h(k) závisí jenom na p nejnižších bitech klíče k. Pokud nevíme, že všechny p - bitové permutace nejnižších bitů jsou stejně pravděpodobné, volíme jako m prvočíslo, které není blízké mocnině 2\. Nastává například při správě paměťových struktur operašního systému, kterých velikost je obvykle 2<sup>p.</sup>

**Klíčem je řetězec ASCII znaků**

ASCII kód zobrazuje znak řetězcem 7 bitů, rozeznává tedy 128 různých hodnot, které můžeme interpretovat jako celá čísla 0 až 127. řetězec α<sub>1</sub>α<sub>2</sub>...α<sub>n</sub> potom můžeme interpretovat jako zápis čísla se základem 128, kterého hodnota je
z<sub>1</sub>.128<sup>n-1</sup> + z<sub>2</sub>.128<sup>n-2</sup> + ... +z<sub>n</sub>.128<sup>0</sup>
kde z<sub>i</sub> je celočíselná interpretace znaku α<sub>i
</sub>Řetězce znaků mohou být tak dlouhé, že uvedená číselná reprezentace přesáhne rozsah zobrazitelných celočíselných hodnot.
Potřebujeme:

h(α<sub>1</sub>α<sub>2</sub>...α<sub>n</sub>) = z<sub>1</sub>.128<sup>n-1</sup> + z<sub>2</sub>.128<sup>n-2</sup> + ... +z<sub>n</sub>.128<sup>0</sup>) mod m

Užitím Hornerova schématu pro výpočet h(α<sub>1</sub>α<sub>2</sub>...α<sub>n</sub>) dostaneme:

h(α<sub>1</sub>α<sub>2</sub>...α<sub>n</sub>) = z<sub>1</sub>.128<sup>n-1</sup> + z<sub>2</sub>.128<sup>n-2</sup> + ... +z<sub>n</sub>.128<sup>0</sup>) mod m = (((z<sub>1</sub>.128 + z<sub>2</sub>).128 + ... + z<sub>n-1</sub>).128 + z<sub>n</sub>) mod m

Využijeme toho, že pro výpočet mod m nebudeme uvažovat nísobky m ve "velkých" členech tj.

(x.c + y) mod m = ((x mod m).c + y) mod m
x = a.m + b
((a.m + b).c + y) mod m = (a.c.m + b.c +y) mod m = (b.c + y) mod m
(((a.m + b) mod m).c + y) mod m = (b.c + y) mod m

(((z<sub>1</sub>.128 + z<sub>2</sub>).128 + ... + z<sub>n-1</sub>).128 + z<sub>n</sub>) mod m = ((((0.128 + z<sub>1</sub>) mod m .128 + z<sub>2</sub>) mod m . 128 + ... + z<sub>n-1</sub>) mod m .128 + z<sub>n</sub>) mod m

### Multiplikativní metoda

Celou část reálného čísla r označíme ⌊r⌋. Je-li c reálné číslo z intervalu 0 ≤ c < 1, potom ⌊m.c⌋ ∈ {0, 1, ..., m-1}. Jsou-li hodnoty klíčů **k** omezeny, a ≤ k < b, potom c = (k-a)/(b-a).
**K** = {1, 2, ..., 10 000}
**m** = 100

a = 1, b = 10 001
h(k) = ⌊m.c⌋ = ⌊m(k-1)/10000⌋ = ⌊(k-1)/100⌋

klíče k = 1, 2, ..., 100 jsou zobrazeny na pozici 0
klíče k = 101, 102, 200 jsou zobrazeny na pozici 1, atd.

Transformace klíče k na multiplikativní koeficient c musí být "znáhodněna" a přitom její výpočet musí zůstat efektivní.

Zvolíme konstancu **A** z intervalu 0 < A < 1\. Jako **c** vezmeme zlomkovou část součinu **kA**, c = kA - ⌊kA⌋
h(k) = ⌊m(kA-⌊kA⌋⌋

#### Implementace

zvolíme m = 2<sup>p</sup>
Nechť celočíselný klíč k je zobrazen slovem s **w** bity.
**A** zvolme ve tvaru **q/2<sup>w</sup>**, **q** je celé číslo zobrazené také jako slovo s **w** bity, 0 < q < 2<sup>w</sup>, kA = kq/2<sup>w
</sup>Celočíselným násobením **kq** získáme hodnotu s **2w** bity uloženou ve dvou slovech **s0** a **s1**, přičemž **s0** označuje slovo s bity nižších řádů součinu, kq = s1 . 2<sup>w</sup> + s0.
k.A = k.q/2<sup>w</sup> = (k.q) . 2<sup>-w</sup> = s1 + s0 . 2<sup>-w</sup> , w bitů slova s0 obsahuje zlomkovou část součinu kA, tj. kA−⌊kA⌋
h(k) = ⌊m(kA-⌊kA⌋)⌋ = ⌊2<sup>p</sup>(kA-⌊kA⌋)⌋, **h(k)** tvoří **p** nejvýznamějších bitů slova **s0**.

![Multiplikativní metoda](../uploads/category/abstraktni-datove-typy/adt-hash-table/attachments/multiplikativni-metoda.png)

Jako konstanta **A** se doporučuje hodnota zlatého poměru φ = 0.618033
**q** volíme tak, aby q/2<sup>w</sup> bylo co nejblíže φ, q≈0.618033 x 2<sup>w</sup>