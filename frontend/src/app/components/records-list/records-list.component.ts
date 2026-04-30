import { Component, OnInit, signal, computed, effect } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { Record } from '../../models/record.model';
import { EditPanelComponent } from '../edit-panel/edit-panel.component';
import './records-list.component.css';

@Component({
  selector: 'app-records-list',
  standalone: true,
  imports: [CommonModule, FormsModule, EditPanelComponent],
  templateUrl: './records-list.component.html',
  styleUrl: './records-list.component.css'
})
export class RecordsListComponent implements OnInit {
  records = signal<Record[]>([]);
  loading = signal(false);
  error = signal<string | null>(null);
  searchTerm = signal('');
  currentPage = signal(1);
  pageSize = signal(5);
  selectedRecord = signal<Record | null>(null);
  isCreateMode = signal(false);
  showEditPanel = signal(false);

  filteredRecords = computed(() => {
    const search = this.searchTerm().toLowerCase();
    if (!search) return this.records();
    return this.records().filter(r =>
      r.number.toLowerCase().includes(search) ||
      r.currentStatus.toLowerCase().includes(search)
    );
  });

  paginatedRecords = computed(() => {
    const filtered = this.filteredRecords();
    const start = (this.currentPage() - 1) * this.pageSize();
    return filtered.slice(start, start + this.pageSize());
  });

  totalPages = computed(() => {
    return Math.ceil(this.filteredRecords().length / this.pageSize());
  });

  constructor(private apiService: ApiService) {
    effect(() => {
      if (this.filteredRecords().length === 0 && this.currentPage() > 1) {
        this.currentPage.set(1);
      }
    });
  }

  ngOnInit(): void {
    this.loadRecords();
  }

  loadRecords(): void {
    this.loading.set(true);
    this.error.set(null);
    this.apiService.getRecords().subscribe({
      next: (data) => {
        this.records.set(data);
        this.loading.set(false);
      },
      error: (err) => {
        this.error.set('Failed to load records');
        this.loading.set(false);
        console.error(err);
      }
    });
  }

  openEditPanel(record: Record): void {
    this.selectedRecord.set(record);
    this.isCreateMode.set(false);
    this.showEditPanel.set(true);
  }

  openCreatePanel(): void {
    this.selectedRecord.set(null);
    this.isCreateMode.set(true);
    this.showEditPanel.set(true);
  }

  closeEditPanel(): void {
    this.showEditPanel.set(false);
    this.selectedRecord.set(null);
    this.isCreateMode.set(false);
  }

  onRecordSaved(updatedRecord: Record): void {
    this.loadRecords();
    this.closeEditPanel();
  }

  previousPage(): void {
    if (this.currentPage() > 1) {
      this.currentPage.update(p => p - 1);
    }
  }

  nextPage(): void {
    if (this.currentPage() < this.totalPages()) {
      this.currentPage.update(p => p + 1);
    }
  }
}
