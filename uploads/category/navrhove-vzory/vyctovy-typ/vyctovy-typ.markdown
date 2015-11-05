Běžně se dosud používá jen jako typově zabezpečená náhrada symbolických konstant.

V Javě má tento návrhový vzor přímo klíčové slovo _enum_, která se používá místo _class_.

Pro jednoduché výčty (kdy instance jsou celočíselné) stačí použít jen seznam názvů - využívá se přímo klíčového slova _enum_.

Jedná se o plnohodnotnou třídu s mnoha výhodnými možnostmi.

Má konečný počet několika předem známých (vyjmenovaných) instancí.

Během chodu programu nelze vytvářet další instance.

Opět má nepřístupný konstruktor.

Všechny instance jsou definovány jako veřejné statické atributy - odkazy záskáváme přímo přes jméno třídy, např. SvetoveStrany.JIH. Pro získání odkazů se nepoužívá statická tovární metoda.

V následujícím příkladě vytvoříme výčtový typ na světové strany:

```
public enum SvetovyStrany {
      SEVER, JIH, VYCHOD, ZAPAD;
}

public class ZkousecEnumu {
    public static void main(String[] args) {
      SvetoveStrany smer = SvetoveStrany.VYCHOD;
      System.out.println(vychod.name()); /** Vypíše: JIH*/
      System.out.println(vychod.ordinal()); /** Vypíše 2 */
    }
}

```