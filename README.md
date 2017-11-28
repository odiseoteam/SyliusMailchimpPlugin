<article class="markdown-body entry-content" itemprop="text"><p><a href="https://odiseo.com.ar/bundles/odiseoapp/images/logoodiseotransparent.png" target="_blank"><img src="https://odiseo.com.ar/bundles/odiseoapp/images/logoodiseotransparent.png" alt="Odiseo" data-canonical-src="https://odiseo.com.ar/bundles/odiseoapp/images/logoodiseotransparent.png" style="margin:0 auto"></a></p>
<h2><a href="#overview" aria-hidden="true" class="anchor" id="user-content-overview"><svg aria-hidden="true" class="octicon octicon-link" height="16" version="1.1" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" d="M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z"></path></svg></a>Overview</h2>
<p>This plugin allows you to integrate MailChimp newsletter</p>
<h2><a href="#support" aria-hidden="true" class="anchor" id="user-content-support"><svg aria-hidden="true" class="octicon octicon-link" height="16" version="1.1" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" d="M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z"></path></svg></a>Support</h2>
<p>Do you want us to customize this plugin for your specific needs? Write us an email on <a href="mailto:odiseo.team@gmail.com">odiseo.team@gmail.com</a> <g-emoji alias="computer" fallback-src="https://assets-cdn.github.com/images/icons/emoji/unicode/1f4bb.png" ios-version="6.0">💻</g-emoji></p>
<h2><a href="#installation" aria-hidden="true" class="anchor" id="user-content-installation"><svg aria-hidden="true" class="octicon octicon-link" height="16" version="1.1" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" d="M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z"></path></svg></a>Installation</h2>
<div class="highlight highlight-source-shell"><pre>$ composer odiseoteam/sylius-mailchimp-plugin
</pre></div>
<p>Add plugin dependencies to your AppKernel.php</p>
<div class="highlight highlight-text-html-php"><pre><span class="pl-s1"><span class="pl-k">public</span> <span class="pl-k">function</span> <span class="pl-en">registerBundles</span>()</span>
<span class="pl-s1">{</span>
<span class="pl-s1">    <span class="pl-k">return</span> <span class="pl-c1">array_merge</span>(<span class="pl-k">parent</span><span class="pl-k">::</span>registerBundles(), [</span>
<span class="pl-s1">        <span class="pl-k">...</span></span>
<span class="pl-s1">        </span>
<span class="pl-s1">        <span class="pl-k">new</span> <span class="pl-c1">\Odiseo\SyliusMailchimpPlugin\OdiseoSyliusMailchimpPlugin</span>(),</span>
<span class="pl-s1">    ]);</span>
<span class="pl-s1">}</span></pre></div>
<h2><a href="#usage" aria-hidden="true" class="anchor" id="user-content-usage"><svg aria-hidden="true" class="octicon octicon-link" height="16" version="1.1" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" d="M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z"></path></svg></a>Usage</h2>
<p>Add MailChimp API key and default list ID to your parameters.yml file</p>
<div class="highlight highlight-source-yaml"><pre><span class="pl-ent">parameters</span>:
    <span class="pl-s">...</span>
    
    <span class="pl-ent">odiseo.mailchimp_plugin.mailchimp_apikey</span>: <span class="pl-s">YOUR_API_KEY</span>
    <span class="pl-ent">odiseo.mailchimp_plugin.mailchimp_default_listid</span>: <span class="pl-s">DEFAULT_LIST_ID</span></pre></div>
<div class="markdown-body entry-content"><p>
/** @var Mailchimp $mailchimp */
$mailchimp = $this->get('odiseo.mailchimp_plugin.mailchimp');
</p></div>
<h2><a href="#contribution" aria-hidden="true" class="anchor" id="user-content-contribution"><svg aria-hidden="true" class="octicon octicon-link" height="16" version="1.1" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" d="M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z"></path></svg></a>Contribution</h2>
<p>Learn more about our contribution workflow on <a href="http://docs.sylius.org/en/latest/contributing/" rel="nofollow">http://docs.sylius.org/en/latest/contributing/</a></p>
</article>
  </div>


  </div>