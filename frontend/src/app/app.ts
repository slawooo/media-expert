import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { RecordsListComponent } from './components/records-list/records-list.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, RecordsListComponent],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App {}

