/**
 * Copyright (C) 2008-2015 www.delight.im <info@delight.im>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

package im.delight.opensoccer;

import im.delight.opensoccer.exceptions.UnknownMenuItemException;
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

public class MainActivity extends Activity implements AdvancedWebView.Listener {

	public static final String QUERY_STRING_ANDROID_INIT = "?via_android=1";
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
        mWebView.loadUrl(Config.SITE_URL + QUERY_STRING_ANDROID_INIT, true);
    }

    private static String getTargetUrlFromMenuItem(final int menuItemId) throws UnknownMenuItemException {
    	switch (menuItemId) {
	    	case R.id.menu_home: return "";
	    	case R.id.menu_office_dashboard: return "";
	    	case R.id.menu_office_reports: return "protokoll.php";
	    	case R.id.menu_office_notes: return "notizen.php";
	    	case R.id.menu_office_settings: return "einstellungen.php";
	    	case R.id.menu_office_logout: return "logout.php";
	    	case R.id.menu_ranking_ranking: return "top_manager.php";
	    	case R.id.menu_ranking_statistics: return "stat_5jahresWertung.php";
	    	case R.id.menu_ranking_manager_of_year: return "manager_der_saison.php";
	    	case R.id.menu_transfers_buy: return "transfermarkt.php";
	    	case R.id.menu_transfers_loan: return "transfermarkt_leihe.php";
	    	case R.id.menu_transfers_watch_list: return "beobachtung.php";
	    	case R.id.menu_transfers_completed: return "lig_transfers.php";
	    	case R.id.menu_transfers_barker: return "marktschreier.php";
	    	case R.id.menu_team_lineup: return "aufstellung.php";
	    	case R.id.menu_team_tactics: return "taktik.php";
	    	case R.id.menu_team_squad: return "kader.php";
	    	case R.id.menu_team_development: return "entwicklung.php";
	    	case R.id.menu_team_contracts: return "vertraege.php";
	    	case R.id.menu_team_calendar: return "kalender.php";
	    	case R.id.menu_season_league: return "lig_tabelle.php";
	    	case R.id.menu_season_international_cup: return "pokal.php";
	    	case R.id.menu_season_national_cup: return "cup.php";
	    	case R.id.menu_season_friendly_matches: return "lig_testspiele_liste.php";
	    	case R.id.menu_season_friendly_market: return "testWuensche.php";
	    	case R.id.menu_club_finances: return "ver_finanzen.php";
	    	case R.id.menu_club_transactions: return "ver_buchungen.php";
	    	case R.id.menu_club_personnel: return "ver_personal.php";
	    	case R.id.menu_club_stadium: return "ver_stadion.php";
	    	case R.id.menu_requests_loans: return "leihgaben.php";
	    	case R.id.menu_requests_friendlies: return "testspiele.php";
	    	case R.id.menu_requests_league_swap: return "ligaTausch.php";
	    	case R.id.menu_support_support: return "support.php";
	    	case R.id.menu_support_contact_staff: return "wio.php#teamList";
	    	case R.id.menu_support_short_hints: return "tipps_des_tages.php";
	    	case R.id.menu_support_rules: return "regeln.php";
	    	default: throw new UnknownMenuItemException("Unknown menu item ID: "+menuItemId);
    	}
    }

    @Override
    public boolean onCreateOptionsMenu(final Menu menu) {
        getMenuInflater().inflate(R.menu.activity_main, menu);
        return true;
    }

	@Override
	public boolean onOptionsItemSelected(final MenuItem item) {
		try {
			final String targetPath = getTargetUrlFromMenuItem(item.getItemId());
			mWebView.loadUrl(Config.SITE_URL + targetPath, true);

			return true;
		}
		catch (UnknownMenuItemException e) {
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
		reallyExit.setTitle(R.string.are_you_sure);
		reallyExit.setMessage(R.string.really_want_to_leave);
		reallyExit.setPositiveButton(R.string.leave, new DialogInterface.OnClickListener() {

			@Override
			public void onClick(DialogInterface dialog, int which) {
				finish();
			}

		});
		reallyExit.setNegativeButton(R.string.cancel, null);
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
