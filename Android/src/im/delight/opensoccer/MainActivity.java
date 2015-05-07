package im.delight.opensoccer;

import android.view.MenuItem;
import im.delight.android.webview.AdvancedWebView;
import android.content.Context;
import android.view.ViewConfiguration;
import java.lang.reflect.Field;
import android.os.Bundle;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.view.Menu;
import android.view.View;
import android.webkit.CookieManager;
import android.webkit.CookieSyncManager;
import android.widget.ProgressBar;
import android.widget.Toast;

public class MainActivity extends Activity implements AdvancedWebView.Listener {

	private AdvancedWebView mWebView;
	private AlertDialog mAlertDialog;
	private ProgressBar mProgressBar;

	@SuppressLint("NewApi")
	@Override
	protected void onResume() {
		super.onResume();

		mWebView.onResume();
		CookieSyncManager.getInstance().startSync();
	}

	@SuppressLint("NewApi")
	@Override
	protected void onPause() {
		CookieSyncManager.getInstance().stopSync();
		mWebView.onPause();

		super.onPause();
	}

    @SuppressLint("SetJavaScriptEnabled")
	@Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_main);

        forceOverflowMenu(this);

        CookieSyncManager.createInstance(this);
        CookieManager.getInstance().setAcceptCookie(true);

        mProgressBar = (ProgressBar) findViewById(R.id.progressBar);

        mWebView = (AdvancedWebView) findViewById(R.id.webview);
        mWebView.setListener(this, this);
        mWebView.loadUrl("http://m.opensoccer.org/?via_android=1", true);

        if (android.os.Build.VERSION.SDK_INT < 11) {
        	Toast.makeText(this, getString(R.string.menu_hint), Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(final Menu menu) {
        getMenuInflater().inflate(R.menu.activity_main, menu);
        return true;
    }

    private void showPageChooser(final CharSequence[] items, final String[] urls, final String title) {
		// TODO rewrite
    	final AlertDialog.Builder chooser = new AlertDialog.Builder(this);
    	chooser.setTitle(title);
    	chooser.setItems(items, new DialogInterface.OnClickListener() {

    		@Override
			public void onClick(DialogInterface dialog, int which) {
				if (dialog != null) {
					dialog.dismiss();
				}
				mWebView.loadUrl("http://m.opensoccer.org"+urls[which], true);
			}

		});
    	chooser.setNeutralButton(getString(R.string.cancel), new DialogInterface.OnClickListener() {

    		@Override
			public void onClick(DialogInterface dialog, int which) {
				if (dialog != null) {
					dialog.dismiss();
				}
			}

		});
    	chooser.show();
    }

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// TODO rewrite
		switch (item.getItemId()) {
		case R.id.menu_buero:
			CharSequence[] items0 = { "Zentrale", "Protokoll", "Notizen", "Einstellungen" };
			String[] urls0 = { "/", "/protokoll.php", "/notizen.php", "/einstellungen.php" };
			showPageChooser(items0, urls0, "Büro");
			return true;
		case R.id.menu_ranking:
			CharSequence[] items1 = { "Ranking", "Statistiken", "Manager-Wahl" };
			String[] urls1 = { "/top_manager.php", "/stat_5jahresWertung.php", "/manager_der_saison.php" };
			showPageChooser(items1, urls1, "Ranking");
			return true;
		case R.id.menu_transfers:
			CharSequence[] items2 = { "Kaufen", "Leihen", "Beobachtung", "Abgeschlossen", "Marktschreier" };
			String[] urls2 = { "/transfermarkt.php", "/transfermarkt_leihe.php", "/beobachtung.php", "/lig_transfers.php", "/marktschreier.php" };
			showPageChooser(items2, urls2, "Transfers");
			return true;
		case R.id.menu_team:
			CharSequence[] items3 = { "Aufstellung", "Taktik", "Kader", "Entwicklung", "Verträge", "Kalender" };
			String[] urls3 = { "/aufstellung.php", "/taktik.php", "/kader.php", "/entwicklung.php", "/vertraege.php", "/kalender.php" };
			showPageChooser(items3, urls3, "Team");
			return true;
		case R.id.menu_saison:
			CharSequence[] items4 = { "Liga", "Int. Pokal", "Nat. Cup", "Testspiele", "Testwünsche" };
			String[] urls4 = { "/lig_tabelle.php", "/pokal.php", "/cup.php", "/lig_testspiele_liste.php", "/testWuensche.php" };
			showPageChooser(items4, urls4, "Saison");
			return true;
		case R.id.menu_verein:
			CharSequence[] items5 = { "Finanzen", "Buchungen", "Personal", "Stadion", "Lotto" };
			String[] urls5 = { "/ver_finanzen.php", "/ver_buchungen.php", "/ver_personal.php", "/ver_stadion.php", "/ver_lotto.php" };
			showPageChooser(items5, urls5, "Verein");
			return true;
		case R.id.menu_anfragen:
			CharSequence[] items6 = { "Leihgaben", "Testspiele", "Ligatausch" };
			String[] urls6 = { "/leihgaben.php", "/testspiele.php", "/ligaTausch.php" };
			showPageChooser(items6, urls6, "Anfragen");
			return true;
		case R.id.menu_community:
			CharSequence[] items7 = { "Chat", "Post: Ein", "Post: Aus", "Freunde", "Sanktionen", "Nutzungsregeln" };
			String[] urls7 = { "/chat.php", "/posteingang.php", "/postausgang.php", "/freunde.php", "/sanktionen.php", "/regeln.php" };
			showPageChooser(items7, urls7, "Community");
			return true;
		case R.id.menu_support:
			CharSequence[] items8 = { "Support", "Post ans Team", "Kurztipps", "Neuigkeiten" };
			String[] urls8 = { "/support.php", "/wio.php#teamList", "/tipps_des_tages.php", "/neuigkeiten.php" };
			showPageChooser(items8, urls8, "Support");
			return true;
		default:
			return super.onOptionsItemSelected(item);
		}
	}

	@Override
	protected void onNewIntent(final Intent i) {
		super.onNewIntent(i);
		setIntent(i);
	}

	@Override
	public void onBackPressed() {
		if (!mWebView.onBackPressed()) { return; }

		final AlertDialog.Builder reallyExit = new AlertDialog.Builder(this);
		reallyExit.setTitle("Wirklich verlassen?");
		reallyExit.setMessage("Möchtest Du das Spiel wirklich beenden?");
		reallyExit.setPositiveButton("Ja", new DialogInterface.OnClickListener() {

			@Override
			public void onClick(DialogInterface dialog, int which) {
				finish();
			}

		});
		reallyExit.setNegativeButton("Nein", null);
		mAlertDialog = reallyExit.show();
	}

	@Override
	protected void onDestroy() {
		if (mAlertDialog != null) {
			mAlertDialog.dismiss();
			mAlertDialog = null;
		}

		mWebView.onDestroy();

		super.onDestroy();
	}

	private static void forceOverflowMenu(Context context) {
		try {
			final ViewConfiguration config = ViewConfiguration.get(context);
			final Field menuKeyField = ViewConfiguration.class.getDeclaredField("sHasPermanentMenuKey");
			if (menuKeyField != null) {
				menuKeyField.setAccessible(true);
				menuKeyField.setBoolean(config, false);
			}
		}
		catch (Exception e) { }
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent intent) {
		super.onActivityResult(requestCode, resultCode, intent);
		mWebView.onActivityResult(requestCode, resultCode, intent);
	}

	@Override
	public void onDownloadRequested(final String url, final String userAgent, final String contentDisposition, final String mimetype, final long contentLength) { }

	@Override
	public void onExternalPageRequest(final String url) { }

	@Override
	public void onPageError(final int errorCode, final String description, final String failingUrl) { }

	@Override
	public void onPageFinished(final String url) {
		mProgressBar.setVisibility(View.GONE);
		mWebView.setVisibility(View.VISIBLE);

		CookieSyncManager.getInstance().sync();
	}

	@Override
	public void onPageStarted(final String url, final Bitmap favicon) {
		mWebView.setVisibility(View.GONE);
		mProgressBar.setVisibility(View.VISIBLE);
	}

}
