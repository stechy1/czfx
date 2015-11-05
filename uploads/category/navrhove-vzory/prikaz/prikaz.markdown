Zabalí metodu (službu) do objektu, takže s ní lze pracovat jako s daty
- typicky předat jako skutečný parametr metody
- zabalenř umožňuje dynamickou výměnu používaných metod i za běhu programu

Příklad z Javy:
*   _java.util.Arrays_ - _sort()__.binarySearch()_
*   _java.util.TreeSet, java.util.TreeMap_

_IStylVypisu_ - definice poskytovaní služby

```
public interface IStylVypisu {
    public void vypisStylem(String slovo);
}

```

_Styl\_Prikaz_ - předpřipravení služby jako statické konstanty (_STANDARDNI, VELKY, MALY, PROLOZENE_) implementované pomocí anonymních vnitřních tříd.

```
public class Styl_Prikaz {
    // konstanty různých stylů výpisu
    public static final IStylVypisu STANDARDNI = new IStylVypisu() {
      public void vypisStylem(String slovo) {
        System.out.print(slovo);
      }
    };
    public static final IStylVypisu VELKY = new IStylVypisu() {
      public void vypisStylem(String slovo) {
        System.out.print(slovo.toUpperCase());
      }
    };
    public static final IStylVypisu MALY = new IStylVypisu() {
      public void vypisStylem(String slovo) {
        System.out.print(slovo.toLowerCase());
      }
    };
    public static final IStylVypisu PROLOZENE = new IStylVypisu() {
      public void vypisStylem(String slovo) {
        for (int i = 0; i < slovo.length(); i++) {
          System.out.print(slovo.charAt(i) + " ");
        }
      }
    };
}

```

Služba implementovaná samostatnou třídou _StylPrikazVelbloud_
- jiný způsob definice služby:

```
public class StylPrikazVelbloud implements IStylVypisu {
    public void vypisStylem(String slovo) {
      String pomocne = slovo.toUpperCase();
      for (int i = 0; i < pomocne.length(); i++) {
        char znak = pomocne.charAt(i);
        znak = (i % 2 == 0) ? znak : Character.toLowerCase(znak);
        System.out.print(znak);
      }
    }
}

```

_VyuzivaStyl_ je jedna z mnoha možných tříd využívající nadřzené služby
pro nejčastěji používaný styl výpisu (tj. _STANDARDNI_) má pro zjednodušení předpřipravenou metodu
srovnej _Arrays.sort(pole)_ a _Arrays.sort(pole, komparator)_

```
public class VyuzivaStyl {
    public static void vypisVeStylu(String[] slova) {
      vypisVeStylu(slova, Styl_Prikaz.STANDARDNI);
    }
    public static void vypisVeStylu(String[] slova, IStylVypisu styl) {
      System.out.print("[");
      for (String slovo : slova) {
        styl.vypisStylem(slovo + " ");
      }
      System.out.println("]");
    }
}

```

_Aplikace_ - ukázka použití různých zápisů jako:
- předpřipravených konstant ze Styl_Prikaz (_VELKY, PROLOZENE_)
- instance třídy _StylPrikazVelbloud_

```
public class Aplikace {
    public static void main(String[] args) {
      String[] slova = {"Kočka", "leze", "dírou",
        "pes", "oknem",
        "nebude-li", "pršet", "nezmoknem" };
      System.out.println(Arrays.toString(slova));
      VyuzivaStyl.vypisVeStylu(slova);
      VyuzivaStyl.vypisVeStylu(slova, Styl_Prikaz.VELKY);
      VyuzivaStyl.vypisVeStylu(slova, Styl_Prikaz.PROLOZENE);
      VyuzivaStyl.vypisVeStylu(slova, new StylPrikazVelbloud());
    }
}

```

Výpis do konzole:

```

[Kočka, leze, dírou, pes, oknem, nebude-li, pršet, nezmoknem]
[Kočka leze dírou pes oknem nebude-li pršet nezmoknem ]
[KOČKA LEZE DÍROU PES OKNEM NEBUDE-LI PRŠET NEZMOKNEM ]
[K o č k a l e z e d í r o u p e s o k n e m n e b u d e - l i ►
p r š e t n e z m o k n e m ]
[KoČkA LeZe DíRoU PeS OkNeM NeBuDe-lI PrŠeT NeZmOkNeM ]

```