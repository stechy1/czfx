Můžeme najít taky pod názvem **jednoduchá tovární metoda** (simple factory method).

Statická metoda, která vrací odkaz na instanci své třídy.

Třída může znepřístupnit konstruktor pomocí _private_, ale na rozdíl od knihovní třídy dokáže/chce vytvářet své instance.

Používá se ke stejnému účelu, jako konstruktor _new_.

Výhody:

-  může se jmenovat libovolně významově - LogManager.getLogManager() (na rozdíl od konstruktoru který má přesně vyhrazené jméno)
-  sama se rozhodne, zda vnitřně opravdu vytvoří novou instanci, nebo použije už nějakou hotovou ze svého vnitřního kontejneru
-  může vracet objekt libovolného podtypu svého návratového typu - API může poskytovat objekty, aniž by zveřejňovalo jejich třídy

příklad - _java.util.Collections_ má 20 pomocných implementací svých kolekcí (_SynchronizedMap, UnmodifiableMap__, ..._)

zveřejnění všech by znamenalo velký a zbytečný nárůst API - stačí jedna třída (tj. _Collections_), která je umí vytvářet

Nevýhody:

- tyto třídy se nedají dědit (to může být ale do jisté míry i výhoda)
- nejasné pojmenování - nelze na první pohled odlišit od ostatních metod
- typické pojmenování:
 - _getInstance()_ - nejběžnější název
 - _getNazevTridy()_
 -  _valuOf()_ - vrací instanci, která má "stejnou" hodnotu, jako její parametry - typicky u _String_, kde _String.valueOf(123_) vrátí řetězec "123"