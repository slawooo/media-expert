import { Component, Input, Output, EventEmitter, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { Record } from '../../models/record.model';
import './edit-panel.component.css';

@Component({
  selector: 'app-edit-panel',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './edit-panel.component.html',
  styleUrl: './edit-panel.component.css'
})
export class EditPanelComponent implements OnInit {
  @Input() record: Record | null = null;
  @Input() isNew = false;
  @Input() panelTitle = 'Edit Record';
  @Output() close = new EventEmitter<void>();
  @Output() save = new EventEmitter<Record>();

  editNumber = signal('');
  editStatus = signal('');
  saving = signal(false);
  error = signal<string | null>(null);

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.editNumber.set(this.record?.number ?? '');
    this.editStatus.set(this.record?.currentStatus ?? '');
  }

  onClose(): void {
    this.close.emit();
  }

  onSave(): void {
    if (!this.editNumber().trim()) {
      this.error.set('Number is required');
      return;
    }

    this.saving.set(true);
    this.error.set(null);

    if (this.isNew) {
      this.apiService.createRecord(this.editNumber(), this.editStatus()).subscribe({
        next: (createdRecord) => {
          this.saving.set(false);
          this.save.emit(createdRecord);
        },
        error: (err) => {
          this.saving.set(false);
          this.error.set('Failed to create record');
          console.error(err);
        }
      });
      return;
    }

    const updatePromises = [];

    if (this.record) {
      if (this.editNumber() !== this.record.number) {
        updatePromises.push(
          this.apiService.updateRecord(this.record.id, {
            number: this.editNumber()
          }).toPromise()
        );
      }

      if (this.editStatus() !== this.record.currentStatus) {
        updatePromises.push(
          this.apiService.updateStatus(this.record.id, this.editStatus()).toPromise()
        );
      }
    }

    if (updatePromises.length === 0) {
      this.saving.set(false);
      this.onClose();
      return;
    }

    Promise.all(updatePromises)
      .then(() => {
        this.saving.set(false);
        if (!this.record) {
          return null;
        }
        return this.apiService.getRecord(this.record.id).toPromise();
      })
      .then((updatedRecord) => {
        if (updatedRecord) {
          this.save.emit(updatedRecord);
        }
      })
      .catch((err) => {
        this.saving.set(false);
        this.error.set('Failed to save changes');
        console.error(err);
      });
  }
}
