Budeme se věnovat zejména dvěma algoritmům

*   Prohledávání do šířky (Breath-first search, BFS)
*   Prohledávání do hloubky (Depth-first search, DFS)

## Prohledávání do šířky (Breath-first search, BFS)

### Popis algoritmu

Z vybraného vrcholu s nalezneme všechny vrcholy ve vzdálenosti k od vrcholu s předtím, než nalezneme vrcholy ve vzdálenosti k+1.

Nalezený vrchol obarvíme šedě a uložíme ho do fronty, přičemž začneme vrcholem s.

Po nalezení všech sousedů vrchol obarvíme černě. Není-li fronta prázdná, pokračujeme dalším vrcholem z fronty.

### Implementace

Další informace v prvcích pole vrcholy[] třídy **Vrchol** jsou:

*   _barva_ - barva uzlu, na začátku bílá
*   _vzdalenost_ - vzdálenost od uzlu _s_, na začátku ∞
*   _predchudce_ - číslo předcházejícího uzlu, na začátku -1 = není žádný předchůdce

```
void BFS (int s) {
    IntFronta f = new IntFronta();
    vrcholy[s].barva = 'S';
    vrcholy[s].vzdalenost = 0;
    f.vloz(s);
    while(!f.jePrazdna()) {
        int u = f.vyber();
        for (všechny vrcholy v sousedící s u) {
            if (vrcholy[v].barva == 'B') {
                vrcholy[v].barva = 'S';
                vrcholy[v].vzdalenost = vrcholy[u].vzdalenost+1;
                vrcholy[v].predchudce = u;
                f.vloz(v);
            }
        }
        vrcholy[u].barva = 'C'; 
    }
}
```

### Příklad

![Příklad grafu po prohledání BFS](../uploads/category/abstraktni-datove-typy/adt-prohledavani-grafu/attachments/prohledavani-bfs.png)

### Složitost

Obílení všech vrcholů při inicializaci trvá O(|V |) (není v BFS).

Po vložení do fronty, vrcholy již nikdy nejsou obíleny, a protože vložení a vybrání z fronty je O(1), trvání těchto operací je O(|V |).

Sousedé všech vrcholů jsou procházeni, jenom jednou, totiž když je vrchol vybrán z fronty, a jejich celková délka je Θ(|H|).

Procházení tedy trvá O(|H|).

Čas BFS je tedy O(|V| + |H|).

### Vlastnosti BFS algoritmu

*   nalezne všechny dosažitelné vrcholy z vybraného vrcholu _s_
*   vypočte vzdálenost (počet hran) k objeveným vrcholům od _s_
*   v grafu _G(V,H)_ definujeme délku nejkratší cesty mezi vrcholy_ s,v∈V_ jako minimální počet hran všech cest z vrcholu s do vrcholu v; délka nejkratší cesty mezi vrcholy s a v potom je rovná hloubce vrcholu _v_ v BFS stromě s kořenem s, která je uložená v položce _vzdalenost_.
*   vytvoří BFS strom všech dosažitelných vrcholů, kterého kořen je _s._

Tisk vrcholů na nejkratší cestě z vrcholu _s_ do vrcholu _v_ po vykonání BFS

```
void tiskCesty(int s, int v) {
    if(v == s)
        System.out.println(s+" ");
    else {
        if(vrcholy[v].predchudce == -1)
            System.out.println("cesta neexistuje");
        else {
            tiskCesty(s,vrcholy[v].predchudce);
        System.out.println(v+" ");
        }
    }
}
```

**Poznámky:**

BFS je analogie průchodu stromem po úrovních.

BFS je pro orientované i neorientované grafy.

BFS je základem dalších algoritmů (Primmův algoritmus minimální kostry, Dijkstrův algoritmus minimální cesty).

## Prohledávání do hloubky (Depth-first search, DFS)

### Popis algoritmu

Hledáme, když je to možné, napřed do „hloubky“, tj. do větší vzdálenosti a nalezené vrcholy obarvíme šedě

Po průchodu všemi hranami z vyšetřovaného vrcholu, vrchol obarvíme černě a vrátíme se k jeho předchůdci nebo skončíme

Kromě položek barva a predchudce zavedeme do dalších informací dvě časové značky (jednotka „času“ = přechod na další vrchol)

### Implementace

Další informace v prvcích pole vrcholy[] třídy **Vrchol** jsou:

*   _objeven_ - čas nalezení vrcholu, kdy je obarven šedě
*   _dokoncen_ - čas konce procházení seznamu sousedů vrcholu, kdy je vrchol obarven černě

Hodnoty časových značek jsou mezi 1 a 2|H|. Pro každý vrchol _u_ platí, že _objeven(u)_ < _dokoncen(u)_.

```
void DFS (int u) {
    vrcholy[u].barva = 'S';
    cas = cas + 1;
    vrcholy[u].objeven = cas;
    for (všechny vrcholy v sousedící s u) {
        if (vrcholy[v].barva == 'B') {
            vrcholy[v].predchudce = u;
            DFS(v);
        }
    }
    vrcholy[u].barva = 'C';
    vrcholy[u].dokoncen = cas = cas + 1;
}
```

### Příklad

![Příklad grafu po prohledání DFS](../uploads/category/abstraktni-datove-typy/adt-prohledavani-grafu/attachments/prohledavani-dfs.png)

### Složitost

V úvodu a závěru metody DFS je každý vrchol obarven napřed šedě a potom černě právě jednou, což trvá O(|V |).

DFS je voláno pro bílé vrcholy, které jsou ihned zašeděny a v DFS se cyklus for vykoná pro všechny hrany z něho vycházející, což je celkem pro všechny vrcholy O(|H|).

Čas výpočtu DFS tedy je O(|V | + |H|).

### Vlastnosti DFS algoritmu

DFS nalezne pro začáteční vrchol DFS strom dosažitelných vrcholů.

Jestliže po vykonání DFS zůstaly neobjevené vrcholy, jeden

vybereme a postup opakujeme, vznikne tak les DFS stromů.

DFS využívají jiné algoritmy.

**Poznámky:**

Analogie ~order průchodu stromem.

DFS je pro orientované i neorientované grafy.

Rekurzivní volání lze odstranit použitím zásobníku.