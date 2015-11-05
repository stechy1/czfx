Sjednocuje typy používaných objektů a umožňuje tak jednotné zpracování každého z nich nezávisle na tom, jedná-li se o koncový (dále nedělitelný) objekt nebo o objekt složený z jiných objektů

V javě třídy:
-   _java.io.File_ - pro soubory i pro adresáře (složky)
-   _java.awt.Component_ a _java.awt.Container_

![Obrázek](../uploads/category/navrhove-vzory/kompozit/attachments/kompozit_tutorial.png)

*IZkouseni_Komponenta* definuje schopnosti kompozitu

```
public interface IZkouseni_Komponenta {
    public void odpovedZkouseneho();
}
```

*Zkouseny_List* jako konečný prvek implementuje schopnosti kompozitu.

```
public class Zkouseny_List implements IZkouseni_Komponenta {
    private static final Random nahodnyTip = new Random(1);
    private static final int POCET_ODPOVEDI = 4;
    private final String jmeno;
    public Zkouseny_List(String jmeno) {
        this.jmeno = jmeno;
    }
    @Override
    public void odpovedZkouseneho() {
        System.out.println(jmeno + "> "
        + (char) (nahodnyTip.nextInt(POCET_ODPOVEDI) + 'a'));
    }
}
```

*ZkousenaSkupina_kompozit* má kontejner na jednotlivé kompozity *skupinaZkousenych.*

Vykonávání shopnosti kompozitu (*odpovedZkouseneho()*) postupně deleguje na jednotlivé prvky kontejneru.

Má metody pro přidání (*add(IZkouseni_Komponenta zkouseny)*) a ubírání (*remove(IZkouseni_Komponenta* *zkouseny)*) kompozitu z kontejneru.

```
public class ZkousenaSkupina_Kompozit implements IZkouseni_Komponenta {
    private final List skupinaZkousenych = new ArrayList();
    @Override
    public void odpovedZkouseneho() {
        for (IZkouseni_Komponenta zkouseny : skupinaZkousenych) {
            zkouseny.odpovedZkouseneho();
        }
    }
    // přidání zkoušeného do skupiny
    public void add(IZkouseni_Komponenta zkouseny) {
       skupinaZkousenych.add(zkouseny);
    }
    // odebrání zkoušeného ze skupiny
    public void remove(IZkouseni_Komponenta zkouseny) {
        skupinaZkousenych.remove(zkouseny);
    }
}
```

*Aplikace* může vytvářet instance kompozitů, jak listů (*Zkouseny_List*), tak i seznamů (*ZkousenaSkupina_Kompozit*) a libovolně je seskupovat, případně skupiny měnit.

```
public class Aplikace {
    public static void main(String[] args) {
        // inicializace zkoušených studentů
        Zkouseny_List studentka1 = new Zkouseny_List("Alena");
        Zkouseny_List studentka2 = new Zkouseny_List("Hana");
        Zkouseny_List student3 = new Zkouseny_List("Pavel");
        Zkouseny_List student4 = new Zkouseny_List("Petr");
        Zkouseny_List studentZahranicni = new Zkouseny_List("John");
        // inicializace zkouškových skupin
        ZkousenaSkupina_Kompozit zeny = new ZkousenaSkupina_Kompozit();
        ZkousenaSkupina_Kompozit muzi = new ZkousenaSkupina_Kompozit();
        ZkousenaSkupina_Kompozit vsichni = new ZkousenaSkupina_Kompozit();
        // přidání studentů do skupin
        zeny.add(studentka1);
        zeny.add(studentka2);
        muzi.add(student3);
        muzi.add(student4);
        vsichni.add(studentZahranicni);
        // vytvoření finální skupiny
        vsichni.add(zeny);
        vsichni.add(muzi);
        // vyzkoušení celé skupiny
        vsichni.odpovedZkouseneho();
        // přezkoušení jedné studentky
        studentka1.odpovedZkouseneho();
        // přezkoušení všech mužů
        vsichni.remove(zeny);
        vsichni.odpovedZkouseneho();
    }
}
```