package com.lateinme.manager;

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
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.WindowManager;
import android.webkit.CookieManager;
import android.webkit.CookieSyncManager;
import android.webkit.JsResult;
import android.webkit.WebChromeClient;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ProgressBar;
import android.widget.Toast;

public class MainActivity extends Activity {
	
	private WebView mWebView = null;
	private AlertDialog mAlertDialog;
	
	@Override
	protected void onResume() {
		super.onResume();
		CookieSyncManager.getInstance().startSync();
	}

	@Override
	protected void onPause() {
		super.onPause();
		CookieSyncManager.getInstance().stopSync();
	}

    @SuppressLint("SetJavaScriptEnabled")
	@Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN);
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
        setContentView(R.layout.activity_main);
        forceOverflowMenu(this);

        CookieSyncManager.createInstance(this);
        CookieManager.getInstance().setAcceptCookie(true);
        mWebView = (WebView) findViewById(R.id.webview);
        final ProgressBar progressBar = (ProgressBar) findViewById(R.id.progressBar);
        mWebView.getSettings().setJavaScriptEnabled(true);
        mWebView.getSettings().setBuiltInZoomControls(false);
        mWebView.setId(234923794);
        mWebView.setSaveEnabled(true);
        mWebView.setWebChromeClient(new WebChromeClient() {
        	@Override
        	public boolean onJsAlert(WebView view, String url, String message, final JsResult result) {
				AlertDialog.Builder reallyExit = new AlertDialog.Builder(MainActivity.this);
				reallyExit.setTitle(getString(R.string.app_name));
				reallyExit.setMessage(message);
				reallyExit.setNeutralButton("OK", new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						if (dialog != null) {
							dialog.dismiss();
						}
						if (result != null) {
							result.confirm();
						}
					}
				});
				mAlertDialog = reallyExit.show();
				return true;
        	}
			@Override
			public boolean onJsConfirm(WebView view, String url, String message, final JsResult result) {
				AlertDialog.Builder reallyExit = new AlertDialog.Builder(MainActivity.this);
				reallyExit.setTitle(getString(R.string.app_name));
				reallyExit.setMessage(message);
				reallyExit.setPositiveButton("OK", new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						if (dialog != null) {
							dialog.dismiss();
						}
						if (result != null) {
							result.confirm();
						}
					}
				});
				reallyExit.setNegativeButton("Abbrechen", new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						if (dialog != null) {
							dialog.dismiss();
						}
						if (result != null) {
							result.cancel();
						}
					}
				});
				mAlertDialog = reallyExit.show();
				return true;
	        }
        });
        mWebView.setWebViewClient(new WebViewClient() {

	        @Override
	        public void onPageStarted(WebView view, String url, Bitmap favicon) {
				progressBar.setVisibility(View.VISIBLE);
				mWebView.setVisibility(View.GONE);
	        }

	        @Override
			public void onPageFinished(WebView view, String url) {
				progressBar.setVisibility(View.GONE);
				mWebView.setVisibility(View.VISIBLE);
			}

        });
        mWebView.setOnTouchListener(new View.OnTouchListener() {
			public boolean onTouch(View v, MotionEvent event) {
                switch (event.getAction()) {
                case MotionEvent.ACTION_DOWN:
                case MotionEvent.ACTION_UP:
                    if (!v.hasFocus()) {
                        v.requestFocus();
                    }
                    break;
                }
                return false;
			}
        });
        mWebView.loadUrl("http://m.opensoccer.org/?via_android=1");
        if (android.os.Build.VERSION.SDK_INT < 11) {
        	Toast.makeText(this, getString(R.string.menu_hint), Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.activity_main, menu);
        return true;
    }
    
    private void showPageChooser(final CharSequence[] items, final String[] urls, final String title) {
    	AlertDialog.Builder chooser = new AlertDialog.Builder(this);
    	chooser.setTitle(title);
    	chooser.setItems(items, new DialogInterface.OnClickListener() {
			public void onClick(DialogInterface dialog, int which) {
				if (dialog != null) {
					dialog.dismiss();
				}
				mWebView.loadUrl("http://m.opensoccer.org"+urls[which]);
			}
		});
    	chooser.setNeutralButton(getString(R.string.cancel), new DialogInterface.OnClickListener() {
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
	
	protected void onNewIntent(Intent i) {
		super.onNewIntent(i);
		setIntent(i);
	}
	
	@Override
	public void onBackPressed() {
	    if (mWebView != null && mWebView.isFocused() && mWebView.canGoBack()) {
	    	mWebView.goBack();       
	    }
	    else {
			AlertDialog.Builder reallyExit = new AlertDialog.Builder(this);
			reallyExit.setTitle("Wirklich verlassen?");
			reallyExit.setMessage("Möchtest Du das Spiel wirklich beenden?");
			reallyExit.setPositiveButton("Ja", new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					if (dialog != null) {
						dialog.dismiss();
					}
					((Activity) MainActivity.this).finish();
				}
			});
			reallyExit.setNegativeButton("Nein", new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int which) {
					if (dialog != null) {
						dialog.dismiss();
					}
				}
			});
			mAlertDialog = reallyExit.show();
	    }
	}
	
	protected void onDestroy() {
		super.onDestroy();
		if (mAlertDialog != null) {
			if (mAlertDialog.isShowing()) {
				mAlertDialog.dismiss();
			}
			mAlertDialog = null;
		}
	}
	
	private static void forceOverflowMenu(Context context) {
		try {
			ViewConfiguration config = ViewConfiguration.get(context);
			Field menuKeyField = ViewConfiguration.class.getDeclaredField("sHasPermanentMenuKey");
			if (menuKeyField != null) {
				menuKeyField.setAccessible(true);
				menuKeyField.setBoolean(config, false);
			}
		}
		catch (Exception e) { }
	}

}
