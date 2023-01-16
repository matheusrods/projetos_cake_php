
USE [RHHealth]
GO

/****** Object:  Table [dbo].[fichas_assistenciais_respostas_log]    Script Date: 04/01/2023 21:19:22 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[fichas_assistenciais_respostas_log](
	[codigo] [int] IDENTITY(1,1) NOT NULL,
	[codigo_ficha_assistencial_questao] [int] NOT NULL,
    [codigo_ficha_assistencial_resposta] [int] NOT NULL,
    [acao_sistema] [tinyint] NOT NULL,
	[resposta] [text] NULL,
	[campo_livre] [varchar](5000) NULL,
	[data_inclusao] [datetime] NOT NULL,
	[codigo_ficha_assistencial] [int] NOT NULL,
	[parentesco] [varchar](50) NULL,
	[observacao] [text] NULL,
	[data_alteracao] [datetime] NULL,
	[codigo_usuario_alteracao] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[codigo] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_respostas_log].[codigo] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_respostas_log].[codigo_ficha_assistencial_questao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_respostas_log].[resposta] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_respostas_log].[campo_livre] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_respostas_log].[data_inclusao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_respostas_log].[codigo_ficha_assistencial] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_respostas_log].[parentesco] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_respostas_log].[observacao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ALTER TABLE [dbo].[fichas_assistenciais_respostas_log] ADD  CONSTRAINT [df_fichas_assistenciais_respostas_log__data_inclusao]  DEFAULT (getdate()) FOR [data_inclusao]
GO

ALTER TABLE [dbo].[fichas_assistenciais_respostas_log]  WITH CHECK ADD  CONSTRAINT [fk_fichas_assistenciais_respostas_log__codigo_ficha_assistencial_questao] FOREIGN KEY([codigo_ficha_assistencial_questao])
REFERENCES [dbo].[fichas_assistenciais_questoes] ([codigo])
GO

ALTER TABLE [dbo].[fichas_assistenciais_respostas_log] CHECK CONSTRAINT [fk_fichas_assistenciais_respostas_log__codigo_ficha_assistencial_questao]
GO

ALTER TABLE [dbo].[fichas_assistenciais_respostas_log]  WITH CHECK ADD  CONSTRAINT [fk_fichas_assistenciais_respostas_log__codigo_fichas_assistenciais] FOREIGN KEY([codigo_ficha_assistencial])
REFERENCES [dbo].[fichas_assistenciais] ([codigo])
GO

ALTER TABLE [dbo].[fichas_assistenciais_respostas_log] CHECK CONSTRAINT [fk_fichas_assistenciais_respostas_log__codigo_fichas_assistenciais]
GO

ALTER TABLE [dbo].[fichas_assistenciais_respostas_log]  WITH CHECK ADD  CONSTRAINT [fk_fichas_assistenciais_respostas_log__codigo_ficha_assistencial_resposta] FOREIGN KEY([codigo_ficha_assistencial_resposta])
REFERENCES [dbo].[fichas_assistenciais_respostas] ([codigo])
GO

ALTER TABLE [dbo].[fichas_assistenciais_respostas_log] CHECK CONSTRAINT [fk_fichas_assistenciais_respostas_log__codigo_ficha_assistencial_resposta]
GO

