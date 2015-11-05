Je dynamická datová struktura, vzdáleně podobná poli.

Prvky vkládáme a vyjímáme na libovolné pozici (místě) v jejich posloupnosti (sekvenci)

Máme tři druhy seznamu:

* Lineární seznam
* Jednosměrný kruhový seznam
* Dvoucestný/dvousměrný spojový seznam

Rozhraní pro ADT seznam:

```
Seznam() // vytvoření prázdného seznamu
boolean jePrazdny( ) // test je-li seznam prázdný
void tiskSeznamu( ) // tisk prvků seznamu
void naZacatek( ) // nastavení okamžité pozice na první prvek
boolean jePosledni( ) // test je-li okamžitá pozice nastavena na poslední prvek
void naDalsiPrvek( ) // nastavení okamžité pozice na pozici následujícího prvku
int ctiKlic( ) // přečti klíč prvku na okamžité pozici
void vloz(int i) // vlož prvek
int vyber( ) // vyber prvek

```

Možnosti implementace:

* _Polem_ - operace vložení a výběru uvnitř seznamu jsou **O(n)**
* _Spojovou strukturou_ - operace vložení a výběru jsou **O(1)**

### **Spojovou strukturou**

Mějme třídu Prvek představující jeden prvek seznamu:

```
class Prvek {
    ... klic;
    Prvek dalsi;
}

```

Seznam je reprezentován referenční proměnnou _prvni_ typu **Prvek**
inicializace:

```
prvni = null
```

test je-li prázdný:

```
if (prvni == null)
```

Okamžitá pozice reprezentována referenční proměnnou _nynejsi_ (pozice), typu **Prvek**

nastavení na začátek:

```
nynejsi = prvni
```

test poslední pozice:

```
if (nynejsi.dalsi == null)
```

posun na další prvek:

```
nynejsi = nynejsi.dalsi
```

procházení seznamem:

```
for (nynejsi = prvni; nynejsi != null; nynejsi = nynejsi.dalsi)
```

nalezení prvku s určitou hodnotou:

```
if (nynejsi.klic = hodnota)
```

vlož prvek za _nynejsi_:

```
if (prvni == null) {
    prvni = novy;
    prvni.dalsi = null;
} else {
    novy.dalsi = nynejsi.dalsi;
    nynejsi.dalsi = novy;
}
nynejsi = novy;

```

![Vlož prvek za nynější](../uploads/category/abstraktni-datove-typy/adt-seznam/attachments/insert.png)

Pro výběr (nynějšího) prvku musíme znát odkaz na předcházející prvek!

_predch_ udržuje odkaz na prvek předcházející okamžité pozici. Je-li okamžitou pozicí první prvek, má proměnná _predch_ hodnotu null

vyber nynejsi:

```
if (prvni == null) {
    prvni = nynejsi.dalsi;
    nynejsi = prvni;
} else {
    predch.dalsi = nynejsi.dalsi;
    if (nynejsi.dalsi == null) {
        nynejsi = prvni;
        predch = null;
    } else
        nynejsi = nynejsi.dalsi;
}

```

![Vyber nynější prvek](../uploads/category/abstraktni-datove-typy/adt-seznam/attachments/select.png)

1\. poznámka: v případě, že jsme odstranili poslední prvek, nastavíme okamžitou pozicici v seznamu na první prvek.

2\. poznámka: uvedená implementace operace pro vložení prvku neumožňuje vložit do neprázdného seznamu prvek na první místo. Můžeme doplnit operace o operaci _vlož na začátek_. S pomocí proměnné _predch_ můžeme napsat implementaci _vloz_pred_, která vloží prvek před okamžitou pozici.

### **Polem**

Při implementaci polem se používají pole dvě. Jedno pole pro klíče a druhe pro indexy prvků.

```
char[] klic = new char[max];
int[] dalsi = new int[max];
```

prvek seznamu - dvojice prvků se stejným indexem

```
dalsi[i] - ukazatel na následující prvek seznamu
dalsi[i] == -1 - poslední prvek
```

**Příklad:**

```
int jmeno = 3;
klic[3] = 'P'; dalsi[3] = [4];
klic[4] = 'A'; dalsi[4] = [7];
klic[7] = 'V'; dalsi[7] = [2]; 
klic[2] = 'E'; dalsi[2] = [6];
klic[6] = 'L'; dalsi[6] = [-1];

int jineJmeno é 9;
klic[9] = 'D'; dalsi[9] = ...;
```

**Správa volných prvků:**

Zásobník všech volných prvků. Spojené prvky v poli _dalsi_. Vrchol prvku v proměnné _volne_.

```
int volne;
```

Přidělení indexu pro další prvek seznamu:

```
int pridelIndex() {
if (volne == -1)
    "neni volny index"
else {
    int i = volny;
    volny = dalsi[i];
    return i;
}
```

Vrácení indexu mezi volné:

```
void uvolniIndex(i) {
    dalsi[i] = volne;
    volne = i;
}
```