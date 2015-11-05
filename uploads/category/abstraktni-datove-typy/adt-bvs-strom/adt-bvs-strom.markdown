<p><strong>BFS</strong> = binární vyhledávací strom</p>
<pre><code class="java">private DVrchol koren;</code></pre>
<p>prázdný strom</p>
<pre><code class="java">koren == null</code></pre>
<h3>Signatura metody hledej</h3>
<pre><code class="java">String hledej(int)</code></pre>
<h4>rekurzivní implementace</h4>
<pre><code class="java">private String hledejR(DVrchol v, int klic) {
    if (v == null)
        return null;
    if (klic == v.klic)
        return v.data;
    if (klic &lt; v.klic)
        return hledejR(v.levy, klic);
    else
        return hledejR(v.pravy, klic); 
}
String hledej(int klic) {
    return hledejR(koren, klic);
}</code></pre>
<p>odstranění koncové rekurze</p>
<pre><code class="java">String hledej(int klic) {
    DVrchol x = koren; 
    while (x != null &amp;&amp; klic != x.klic)
        if (klic &lt; x.klic )
            x = x.levy;
        else
            x = x.pravy;
    return x == null ? null : x.data;
}</code></pre>
<h4>Nalezení minimálního a maximálního prvku (neprázdného BVS)</h4>
<pre><code class="java">int minKlic() {
    DVrchol x = koren;
    while (x.levy != null)
        x = x.levy;
    return x.klic;
}</code></pre>
<pre><code class="java">int maxKlic() {
    DVrchol x = koren;
    while (x.pravy != null)
        x = x.pravy;
    return x.klic;
}</code></pre>
<p>Vložení a výběr prvku</p>
<p><em>predch</em> - ukazatel na předchůdce</p>
<pre><code class="java">private class DVrchol {
    int klic;
    String data;
    DVrchol levy;
    DVrchol pravy;
    DVrchol predch;

    DVrchol (int klic, String data) {
        this.klic = klic;
        this.data = data;
    }
    void tiskVrcholu() {
        System.out.print(data+" ");
    }
}</code></pre>
<h4>Signatura metody vloz</h4>
<pre><code class="java">void vloz(int, String)</code></pre>
<pre><code class="java">void vloz (int klic, String data) {
    DVrchol x = koren, predch = null;
    while (x != null ) {
        predch = x;
        if (klic &lt; x.klic
             x = x.levy;
        else
            x = x.pravy;
    } 
    DVrchol z = new DVrchol(klic, data);
    z.predch = predch;
    if (predch == null)
        koren = z;
    else if (klic &lt; predch.klic) 
        predch.levy = z;
    else
        predch.pravy = z;
}</code></pre>
<p>Průchod <em>inorder</em> BVS vytiskne tyto prvky uspořádané vzestupně podle klíče.</p>
<h4>Signatura metody vyber</h4>
<pre><code class="java">void vyber(int)</code></pre>
<pre><code class="java">void vyber(int klic) {
    // najdeme vrchol z na vylouceni
    DVrchol z = koren;
    while (z != null &amp;&amp; klic != z.klic)
        if (klic &lt; z.klic)
            z = z.levy;
        else
            z = z.pravy;
    // urcime vrchol y na odstraneni
    DVrchol y = z;
    if (z.levy != null &amp;&amp; z.pravy != null) {
        y = z.pravy;
        while (y.levy != null)
            y = y.levy;
    }
    // x ukazuje na naslednika y anebo je null,
    // kdyz nema zadneho naslednika
    DVrchol x;
    if (y.levy != null)
        x = y.levy;
    else
        x = y.pravy;
    // modifikaci y.predch a x podpojime y
    if (x != null)
        x.predch = y.predch;
    if (y.predch == null)
        koren = x;
    else
        if (y == y.predch.levy)
            y.predch.levy = x;
        else
            y.predch.pravy = x;
    // nebyl-li odpojen z, skopirujeme klic a data
    if (y != z) {
        z.klic = y.klic;
        z.data = y.data;
    }
    // uvolnime y
    y = null;
}</code></pre>
<h3>Vlastnosti BVS</h3>
<p>h - výška BVS</p>
<p>Složitost hledání a vkládání je O(h)</p>
<p>N - počet prvků (vrcholů)</p>
<p>nejhorší případ: h = N - 1, pak je složitost O(N)</p>
<p>nejlepší případ: kromě poslední úrovně, jsou zaplněny všechny vyšší úrovně</p>
<p>h = log2 N, složitost je O(log N)</p>