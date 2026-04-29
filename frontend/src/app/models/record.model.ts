export interface StatusLog {
  id: number;
  status: string;
  createdAt: string;
}

export interface Record {
  id: number;
  number: string;
  createdAt: string;
  currentStatus: string;
  statusHistory?: StatusLog[];
}
