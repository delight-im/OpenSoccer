package im.delight.opensoccer.exceptions;

public class UnknownMenuItemException extends Exception {

	private static final long serialVersionUID = -7701241348434238734L;

	public UnknownMenuItemException() {
		super();
	}

	public UnknownMenuItemException(String detailMessage) {
		super(detailMessage);
	}

	public UnknownMenuItemException(Throwable throwable) {
		super(throwable);
	}

	public UnknownMenuItemException(String detailMessage, Throwable throwable) {
		super(detailMessage, throwable);
	}

}
