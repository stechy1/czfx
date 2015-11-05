**Graf** je základním objektem teorie grafů. Jedná se o reprezentaci množiny objektů, u které chceme znázornit, že některé prvky jsou propojeny. Objektům se přiřadí **vrcholy** a jejich propojení značí **hrany** mezi nimi. Grafy slouží jako abstrakce mnoha různých problémů. Často se jedná o zjednodušený model nějaké skutečné sítě (například dopravní), který zdůrazňuje topologické vlastnosti objektů (vrcholů) a zanedbává geometrické vlastnosti, například přesnou polohu.

![Příklad grafu](../uploads/category/abstraktni-datove-typy/adt-graf/attachments/mapa-graf.png)

### Základní pojmy

*   _prvek_ - vrchol
*   _vztah_ - hrana

### Definice

G = (V,H), kde V je množina vrcholů (uzlů), |V| = počet vrcholů; H je množina hran, |H| = počet hran

_hrana_ je dvojice (u,v), kde u,v∈V

Pro orientovanou hranu platí, že: (u,v) ≠ (v,u)

V(G) - množina vrcholů grafu G

H(G) - množina hran grafu G

#### Příklad neorientovaného grafu

![Příklad neorientovaného grafu](../uploads/category/abstraktni-datove-typy/adt-graf/attachments/neorientovany-graf.png)

#### Příklad orientovaného grafu

![Příklad orientovaného grafu](../uploads/category/abstraktni-datove-typy/adt-graf/attachments/orientovany-graf.png)

### Reprezentace grafu v informatice

*   seznamy sousednosti
*   matice sousednosti

#### Seznamy sousednosti

Pro každý vrchol je vytvořen seznam sousedů. Sousedi jsou uloženi v libovolném pořadí.

Celková délka seznamů sousednosti pro orientovaný graf je |H|

#### Reprezentace neorientovaného grafu

![Příklad neorientovaného grafu reprezentovaného seznamem](../uploads/category/abstraktni-datove-typy/adt-graf/attachments/reprezentace-neorientovaneho-grafu-seznamem.png)

#### Reprezentace orientovaného grafu

![Příklad orientovaného grafu reprezentovaného seznamem](../uploads/category/abstraktni-datove-typy/adt-graf/attachments/reprezentace-orientovaneho-grafu-seznamem.png)

### Implementace polem

Označme vrcholy šísly 0, ...., |V|-1

**vrchol:**

```
class Vrchol {
    ... // další položky pro informace o vrcholu
    Soused sousedi;
    Vrchol () {
        ... // inicializace dalších položek
        sousedi = null;
    } 
}
```

**sousedi:**

```
class Soused {
    int vrchol;
    ... // další položky pro informace o hraně
    Soused dalsi;
    Soused (int v) {
        ...// inicializace dalších položek
        vrchol = v;
        dalsi = null;
    }
}
```

graf = pole vrcholů se seznamy sousedů

```
Vrchol[] vrcholy = new Vrchol[|V|];
```

vložení hrany

```
void hrana(int z, int kam) {
    Soused s = new Soused(kam);
    s.dalsi = vrcholy[z].sousedi;
    vrcholy[z].sousedi = s;
}
```

Reprezentace seznamem sousednosti je vhodná i pro ohodnocené grafy. Zjišťování existence hrany (u, v) = hledání vrcholu _v_ v seznamu sousedů vrcholu u - O(|V|)

### Matice sousednosti

Označme vrcholy čísly 1, ..., |V|.

Matice sousednosti je matice S=(s<sub>ij</sub>), i,j = 1, ..., |V|, přičemž je-li (i,j) ∈H, s<sub>ij</sub> = 1, jinak s<sub>ij</sub> = 0

#### Reprezentace neorientovaného grafu

![Příklad neorientovaného grafu reprezentovaného maticí sousednosti](../uploads/category/abstraktni-datove-typy/adt-graf/attachments/reprezentace-neorientovaneho-grafu-matici.png)

#### Reprezentace orientovaného grafu

![Příklad orientovaného grafu reprezentovaného maticí sousednosti](../uploads/category/abstraktni-datove-typy/adt-graf/attachments/reprezentace-orientovaneho-grafu-matici.png)

graf = dvourozměrné pole

```
int[][] s = new int[|V|][|V|]
```

**Poznámky**

Indexy v Javě budou 0 až |V| - 1

Pro neorientovaný graf je S = S<sup>T</sup>, kde S<sup>T</sup> je transponovaná matice, což umožňuje snížit nároky na paměť téměř na polovinu.

Maticí sousednosti lze implementovat i ohodnocený graf. Pro ohodnocený graf je  s<sub>uv</sub> = w(u, v), je-li (u, v) ∈H. s<sub>uv</sub> je hodnota mimo hodnot možných ohodnocení, je-li (u, v) ∉ H(G)

V případě neohodnoceného grafu, možno prvky matice sousednosti uložit v bitech.

### Jakou zvolit implementaci?

Je-li |H| << |V|<sup>2</sup> graf se nazývá řídký a obvykle je vhodnější použít seznam sousednosti.

Je-li |H| ∼ |V|<sup>2</sup> graf se nazývá hstý a obvykle je vhodnější použít matici sousednosti.

Matici sousednosti je vhodnější použít také, je-li nutno rychle zjistit existenci hrany.