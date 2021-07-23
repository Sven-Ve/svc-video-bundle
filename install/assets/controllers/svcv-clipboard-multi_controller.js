import { Controller } from 'stimulus';
import Swal from 'sweetalert2';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = { link: String, link1: String, link2: String, link3: String };

  connect() {
    if (navigator.clipboard) {
      this.element.classList.remove("d-none");
    };
  }

  copy() {
    this.updateClipboard(this.linkValue);
  }

  copy1() {
    this.updateClipboard(this.link1Value);
  }

  copy2() {
    this.updateClipboard(this.link2Value);
  }

  copy3() {
    this.updateClipboard(this.link3Value);
  }

  updateClipboard(newClip) {
    try {
      navigator.clipboard.writeText(newClip).then(function () {
        Swal.fire({
          title: "Copied.",
          icon: 'success',
          timer: 1500
        })
      }, function() {
        this.displayError();
      });
    } catch (err) {
      this.displayError();
    }
  }

  displayError() {
    Swal.fire({
      title: "Copy failed",
      icon: 'error',
      timer: 1500
    });
  }
}
