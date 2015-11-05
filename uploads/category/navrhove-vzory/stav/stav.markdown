Zavedením několika vnitřních stavů řeší výrazný rozdíl mezi chováním objektu v jeho navenek různých stavech

- změnu stavu objektu řeší záměnou objektu reprezentujícího stav
- objekt pak vypadá, jako by měnil svoji třídu

bez znalosti tohoto návrhového vzoru se podobná úloha řeší pomocí (rozsáhlých) příkazů _switch_ a/nebo větví _if-else-if_

tím, že se používají (vnitřní) "jednostavové" třídy, je jejich kód výrazně jednodušší

- další možné stavy se pak přidávají velmi snadno a pouze na jednom místě třídy - nikoliv v mnoha jejích metodách

příklad ze života - kurzor myši

- _AVnitrniStav_ společný předek vnitřních stavů
- zde se stav bude lišit jen rozdílným způsobem výpisu

```
public abstract class AVnitrniStav {
    public void vypisNazevVnitrnihoStavu() {
      System.out.print(getClass().getSimpleName() + "> ");
    }
    public abstract void vypis(String retezec);
}

```

_Maly\_VnitrniStav_ - jeden z možných vnitřních stavů

```
public class Maly_VnitrniStav extends AVnitrniStav {
    @Override
    public void vypis(String retezec) {
      vypisNazevVnitrnihoStavu();
      System.out.println(retezec.toLowerCase());
    }
}

```

_Velky\_VnitrniStav_ - jeden z možných vnitřních stavů

```
public class Velky_VnitrniStav extends AVnitrniStav {
    @Override
    public void vypis(String retezec) {
      vypisNazevVnitrnihoStavu();
      System.out.println(retezec.toUpperCase());
    }
}

```

_Velbloud\_VnitrniStav_ - jeden z možných vnitřních stavů

```
public class Velbloud_VnitrniStav extends AVnitrniStav {
    @Override
    public void vypis(String retezec) {
      vypisNazevVnitrnihoStavu();
      String pomocne = retezec.toUpperCase();
      for (int i = 0; i < pomocne.length(); i++) {
        char znak = pomocne.charAt(i);
        znak = (i % 2 == 0) ? znak : Character.toLowerCase(znak);
        System.out.print(znak);
      }
      System.out.println();
    }
}

```

_VnejsiStav_ má v sobě jako konstanty všechny dostupné vnitřní stavy

- _aktualniVnitrniStav_ - může se zvnějšku měnit pomocí setru
- jeho metoda _vypis(String retezec)_ jen deleguje pravomoc na metodu z aktuálního vnitřního stavu

```
public class VnejsiStav {
    public static final AVnitrniStav MALY = new Maly_VnitrniStav();
    public static final AVnitrniStav VELKY = new Velky_VnitrniStav();
    public static final AVnitrniStav VELBLOUD = new Velbloud_VnitrniStav();
    private AVnitrniStav aktualniVnitrniStav = MALY;
    public void setStav(AVnitrniStav novyVnitrniStav) {
      this.aktualniVnitrniStav = novyVnitrniStav;
    }
    public void vypis(String retezec) {
      aktualniVnitrniStav.vypis(retezec);
    }
}

```

_ExterniUdalost_ třída, která podle vnějších podmínek mění vnější stav

- změna stavu je na základě délky vypisovaného řetězce
- tato třída není vždy nutná - _VnejsiStav_ by se mohl měnit i sám

```
public class ExterniUdalost {
    private VnejsiStav stav;
    public ExterniUdalost(VnejsiStav stav) {
      this.stav = stav;
    }
    public void zmenVnitrniStav(String retezec) {
      int delka = retezec.length();
      if (delka < 4) {
        stav.setStav(VnejsiStav.VELKY);
      } else if (delka < 7) {
        stav.setStav(VnejsiStav.MALY);
      } else {
       stav.setStav(VnejsiStav.VELBLOUD);
      }
    }
}

```

```
public class Aplikace {
    public static void main(String[] args) {
      VnejsiStav stav = new VnejsiStav();
      ExterniUdalost eu = new ExterniUdalost(stav);
      String[] retezce = {"Jedna", "Dva", "Dvanact", "Osm", "Jedenact" };
      for (String retezec : retezce) {
        eu.zmenVnitrniStav(retezec);
        stav.vypis(retezec);
      }
    }
}

```

Výpis do konzole:

```
Maly_VnitrniStav> jedna
Velky_VnitrniStav> DVA
Velbloud_VnitrniStav> DvAnAcT
Velky_VnitrniStav> OSM
Velbloud_VnitrniStav> JeDeNaCt
```